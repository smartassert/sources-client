<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\SourcesClient\RequestHandler\FileHandler;
use SmartAssert\SourcesClient\RequestHandler\SourceHandler;

class Client
{
    public function __construct(
        public readonly FileHandler $fileSourceFileHandler,
        public readonly SourceHandler $sourceHandler,
    ) {
    }
}
