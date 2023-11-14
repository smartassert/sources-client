<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

use SmartAssert\ServiceClient\SerializableInterface;

readonly class Suite implements SerializableInterface
{
    /**
     * @param non-empty-string   $id
     * @param non-empty-string   $sourceId
     * @param non-empty-string   $label
     * @param non-empty-string[] $tests
     * @param null|int<0, max>   $deletedAt
     */
    public function __construct(
        private string $id,
        private string $sourceId,
        private string $label,
        private array $tests,
        private ?int $deletedAt,
    ) {
    }

    /**
     * @return non-empty-string
     */
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

    /**
     * @return array{
     *   id: non-empty-string,
     *   source_id: non-empty-string,
     *   label: non-empty-string,
     *   tests: non-empty-string[],
     *   deleted_at?: int
     * }
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'source_id' => $this->sourceId,
            'label' => $this->label,
            'tests' => $this->tests,
        ];

        $deletedAt = $this->getDeletedAt();
        if (null !== $deletedAt) {
            $data['deleted_at'] = $deletedAt;
        }

        return $data;
    }
}
