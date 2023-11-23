<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\ServiceClient\Response\ResponseInterface;
use SmartAssert\SourcesClient\Exception\DuplicateFilePathException;
use SmartAssert\SourcesClient\Exception\FilesystemException;
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
        $data = new ArrayInspector($response->getData());

        if (!$data->has('error', 'array')) {
            return InvalidModelDataException::fromJsonResponse(ResponseException::class, $response);
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

        if ('duplicate_file_path' === $type) {
            $path = $payload['path'];
            $path = is_string($path) ? $path : '';

            return new DuplicateFilePathException($path, $response->getHttpResponse(), $type, $payload);
        }

        return new NonSuccessResponseException($response);
    }
}
