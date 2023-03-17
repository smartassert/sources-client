<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

interface RequestInterface
{
    public function hasId(): bool;

    /**
     * @return null|non-empty-string
     */
    public function getId(): ?string;

    /**
     * @return array<mixed>
     */
    public function getPayload(): array;
}
