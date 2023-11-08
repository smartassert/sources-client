<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class FileSourceRequest implements RequestInterface
{
    /**
     * @param 'POST'|'PUT'          $method
     * @param null|non-empty-string $resourceId
     */
    public function __construct(
        private readonly string $method,
        private readonly string $label,
        private readonly ?string $resourceId = null,
    ) {
    }

    /**
     * @return null|non-empty-string
     */
    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }

    public function getPayload(): array
    {
        return ['type' => 'file', 'label' => $this->label];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRoute(): string
    {
        return 'file_source';
    }
}
