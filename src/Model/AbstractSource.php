<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

use SmartAssert\ServiceClient\SerializableInterface;

abstract class AbstractSource implements SourceInterface, SerializableInterface
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

    /**
     * @return array{id: non-empty-string, label: non-empty-string, type: non-empty-string, deleted_at?: int}
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
        ];

        $deletedAt = $this->getDeletedAt();
        if (null !== $deletedAt) {
            $data['deleted_at'] = $deletedAt;
        }

        return $data;
    }
}
