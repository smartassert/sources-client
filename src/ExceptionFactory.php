<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\ServiceClient\Response\ResponseInterface;
use SmartAssert\SourcesClient\Exception\InvalidRequestException;
use SmartAssert\SourcesClient\Exception\ResponseException;

class ExceptionFactory
{
    /**
     * @throws InvalidResponseDataException
     */
    public function createFromResponse(ResponseInterface $response): HttpResponseExceptionInterface
    {
        if (400 === $response->getStatusCode() && $response instanceof JsonResponse) {
            return $this->createFromJsonResponse($response);
        }

        return new NonSuccessResponseException($response);
    }

    /**
     * @throws InvalidResponseDataException
     */
    private function createFromJsonResponse(JsonResponse $response): HttpResponseExceptionInterface
    {
        $errorData = $response->getData()['error'] ?? null;
        if (!is_array($errorData)) {
            return InvalidModelDataException::fromJsonResponse(ResponseException::class, $response);
        }

        $type = $errorData['type'] ?? null;
        $type = is_string($type) ? $type : null;
        $type = '' === $type ? null : $type;

        $payload = $errorData['payload'] ?? [];
        $payload = is_array($payload) ? $payload : [];

        if ('invalid_request' === $type) {
            return new InvalidRequestException($response->getHttpResponse(), $type, $payload);
        }

        return new NonSuccessResponseException($response);
    }
}
