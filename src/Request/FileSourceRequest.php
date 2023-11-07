<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class FileSourceRequest implements RequestInterface
{
    /**
     * @param non-empty-string      $label
     * @param null|non-empty-string $id
     */
    public function __construct(
        private readonly string $label,
        private readonly ?string $id = null,
    ) {
    }

    public function hasId(): bool
    {
        return is_string($this->id);
    }

    /**
     * @return null|non-empty-string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPayload(): array
    {
        return ['type' => 'file', 'label' => $this->label];
    }
}
