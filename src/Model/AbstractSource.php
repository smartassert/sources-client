<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

abstract class AbstractSource
{
    /**
     * @param non-empty-string $id
     */
    public function __construct(
        public readonly string $id,
    ) {
    }
}
