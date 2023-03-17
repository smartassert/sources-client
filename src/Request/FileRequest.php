<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class FileRequest implements RequestInterface
{
    /**
     * @param non-empty-string      $label
     * @param null|non-empty-string $is
     */
    public function __construct(
        private readonly string $label,
        private readonly ?string $is = null,
    ) {
    }

    public function hasId(): bool
    {
        return is_string($this->is);
    }

    /**
     * @return null|non-empty-string
     */
    public function getId(): ?string
    {
        return $this->is;
    }

    public function getPayload(): array
    {
        return ['type' => 'file', 'label' => $this->label];
    }
}
