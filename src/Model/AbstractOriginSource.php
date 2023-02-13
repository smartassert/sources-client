<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

abstract class AbstractOriginSource extends AbstractSource implements OriginSourceInterface
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $userId
     * @param non-empty-string $label
     */
    public function __construct(
        string $id,
        string $userId,
        string $type,
        private readonly string $label,
    ) {
        parent::__construct($id, $userId, $type);
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
