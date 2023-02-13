<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

abstract class AbstractSource implements SourceInterface
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $type
     */
    public function __construct(
        public readonly string $id,
        private readonly string $type,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
