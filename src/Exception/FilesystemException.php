<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Exception;

use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;

class FilesystemException extends ResponseException implements HttpResponseExceptionInterface
{
    /**
     * @param array<mixed> $payload
     */
    public function __construct(
        ResponseInterface $response,
        string $type,
        public readonly array $payload,
    ) {
        parent::__construct($response, $type, $this->payload);
    }

    public function getPayloadFile(): string
    {
        return $this->getPayloadStringValue('file');
    }

    public function getPayloadMessage(): string
    {
        return $this->getPayloadStringValue('message');
    }
}
