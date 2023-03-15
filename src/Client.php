<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

class Client
{
    public function __construct(
        public readonly FileClient $fileClient,
        public readonly SourceClient $sourceClient,
    ) {
    }
}
