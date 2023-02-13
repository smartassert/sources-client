<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

abstract class AbstractOriginSource extends AbstractSource implements OriginSourceInterface
{
    /**
     * @param non-empty-string $label
     * @param non-empty-string $id
     */
    public function __construct(
        string $id,
        string $type,
        private readonly string $label,
    ) {
        parent::__construct($id, $type);
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
