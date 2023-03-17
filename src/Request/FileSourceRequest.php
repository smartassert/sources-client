<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class FileSourceRequest implements SourceRequestInterface
{
    /**
     * @param non-empty-string      $label
     * @param null|non-empty-string $sourceId
     */
    public function __construct(
        private readonly string $label,
        private readonly ?string $sourceId = null,
    ) {
    }

    public function hasId(): bool
    {
        return is_string($this->sourceId);
    }

    /**
     * @return null|non-empty-string
     */
    public function getId(): ?string
    {
        return $this->sourceId;
    }

    public function getPayload(): array
    {
        return ['type' => 'file', 'label' => $this->label];
    }
}
