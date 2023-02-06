<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class InvalidRequestField
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        public readonly string $name,
        public readonly string $value,
        public readonly string $message,
    ) {
    }
}
