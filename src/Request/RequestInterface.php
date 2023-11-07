<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

interface RequestInterface
{
    public function hasResourceId(): bool;

    /**
     * @return null|non-empty-string
     */
    public function getResourceId(): ?string;

    /**
     * @return array<mixed>
     */
    public function getPayload(): array;
}
