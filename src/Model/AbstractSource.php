<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

abstract class AbstractSource implements SourceInterface
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $userId
     * @param non-empty-string $type
     * @param non-empty-string $label
     * @param null|int<0, max> $deletedAt
     */
    public function __construct(
        private readonly string $id,
        private readonly string $userId,
        private readonly string $type,
        private readonly string $label,
        private readonly ?int $deletedAt,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getType(): string
    {
        return $this->type;
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
