<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class FileSource extends AbstractSource
{
    /**
     * @param non-empty-string $label
     * @param non-empty-string $id
     */
    public function __construct(
        public readonly string $label,
        string $id
    ) {
        parent::__construct($id);
    }
}
