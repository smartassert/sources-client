<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\ServiceClient\Response\Response;
use SmartAssert\SourcesClient\Exception\FilesystemException;
use SmartAssert\SourcesClient\Exception\InvalidRequestException;
use SmartAssert\SourcesClient\Model\ErrorInterface;

class ExceptionFactory
{
    /**
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function createFromResponse(Response $response): HttpResponseExceptionInterface
    {
        if (400 === $response->getStatusCode() && $response instanceof JsonResponse) {
            return $this->createFromJsonResponse($response);
        }

        return new NonSuccessResponseException($response->getHttpResponse());
    }

    /**
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    private function createFromJsonResponse(JsonResponse $response): HttpResponseExceptionInterface
    {
        $data = new ArrayInspector($response->getData());

        if (!$data->has('error', 'array')) {
            return InvalidModelDataException::fromJsonResponse(ErrorInterface::class, $response);
        }

        $errorDataInspector = new ArrayInspector($data->getArray('error'));

        $type = $errorDataInspector->getNonEmptyString('type');
        $payload = $errorDataInspector->getArray('payload');

        if ('invalid_request' === $type) {
            return new InvalidRequestException($response->getHttpResponse(), $type, $payload);
        }

        if (is_string($type) && str_starts_with($type, 'source_')) {
            return new FilesystemException($response->getHttpResponse(), $type, $payload);
        }

        return new NonSuccessResponseException($response->getHttpResponse());
    }
}
