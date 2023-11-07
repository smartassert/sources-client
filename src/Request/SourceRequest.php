<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class SourceRequest implements RequestInterface
{
    /**
     * @param 'DELETE'|'GET'        $method
     * @param null|non-empty-string $id
     */
    public function __construct(
        private readonly string $method,
        private readonly ?string $id = null,
    ) {
    }

    /**
     * @return null|non-empty-string
     */
    public function getResourceId(): ?string
    {
        return $this->id;
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
        return 'source';
    }
}
