<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

interface RequestInterface
{
    /**
     * @return null|non-empty-string
     */
    public function getResourceId(): ?string;

    /**
     * @return array<mixed>
     */
    public function getPayload(): array;

    /**
     * @return 'DELETE'|'GET'|'POST'|'PUT'
     */
    public function getMethod(): string;

    /**
     * @return non-empty-string
     */
    public function getRoute(): string;
}
