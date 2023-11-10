<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class FileRequest implements RequestInterface
{
    /**
     * @param 'DELETE'|'GET'|'POST' $method
     */
    public function __construct(
        private readonly string $method,
        private readonly string $resourceId,
        private readonly string $filename,
    ) {
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getPayload(): array
    {
        return [];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRoute(): string
    {
        return 'file';
    }
}
