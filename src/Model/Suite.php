<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class Suite
{
    /**
     * @param non-empty-string   $id
     * @param non-empty-string   $sourceId
     * @param non-empty-string[] $tests
     * @param null|int<0, max>   $deletedAt
     */
    public function __construct(
        private readonly string $id,
        private readonly string $sourceId,
        private readonly string $label,
        private readonly array $tests,
        private readonly ?int $deletedAt,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    /**
     * @return non-empty-string[]
     */
    public function getTests(): array
    {
        return $this->tests;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDeletedAt(): ?int
    {
        return $this->deletedAt;
    }
}
