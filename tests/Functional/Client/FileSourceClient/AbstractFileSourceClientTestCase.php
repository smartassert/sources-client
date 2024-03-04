<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\FileSourceClient;

use SmartAssert\SourcesClient\FileSourceClient;
use SmartAssert\SourcesClient\SourceFactory;
use SmartAssert\SourcesClient\Tests\Functional\Client\AbstractClientTestCase;

abstract class AbstractFileSourceClientTestCase extends AbstractClientTestCase
{
    protected FileSourceClient $fileSourceClient;

    protected function setUp(): void
    {
        parent::setUp();

        $sourceFactory = new SourceFactory();

        $this->fileSourceClient = new FileSourceClient(
            $this->serviceClient,
            $sourceFactory,
            $this->exceptionFactory,
            'https://sources.example.com'
        );
    }
}
