<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

class Client
{
    public function __construct(
        public readonly SourceClient $sourceClient,
    ) {
    }
}
