<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Message\ResponseInterface;
use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\SourcesClient\Model\ErrorInterface;
use SmartAssert\SourcesClient\Model\FilesystemError;
use SmartAssert\SourcesClient\Model\InvalidRequestError;

class ErrorFactory
{
    public function create(ResponseInterface $httpResponse, ArrayInspector $data): ?ErrorInterface
    {
        if (!$data->has('error', 'array')) {
            return null;
        }

        $errorDataInspector = new ArrayInspector($data->getArray('error'));

        $type = $errorDataInspector->getNonEmptyString('type');
        $payload = $errorDataInspector->getArray('payload');

        if ('invalid_request' === $type) {
            return new InvalidRequestError($httpResponse, $payload);
        }

        if (is_string($type) && str_starts_with($type, 'source_')) {
            return new FilesystemError($httpResponse, $type, $payload);
        }

        return null;
    }

    /**
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     * @throws InvalidModelDataException
     */
    public function createFromJsonResponse(JsonResponse $response): ErrorInterface
    {
        $error = $this->create(
            $response->getHttpResponse(),
            new ArrayInspector($response->getData())
        );

        if (null === $error) {
            throw InvalidModelDataException::fromJsonResponse(ErrorInterface::class, $response);
        }

        return $error;
    }
}
