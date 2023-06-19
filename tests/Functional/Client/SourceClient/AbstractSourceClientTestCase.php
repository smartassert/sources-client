<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\SourceClient;

use SmartAssert\SourcesClient\SourceClient;
use SmartAssert\SourcesClient\SourceFactory;
use SmartAssert\SourcesClient\Tests\Functional\Client\AbstractClientTestCase;

abstract class AbstractSourceClientTestCase extends AbstractClientTestCase
{
    protected SourceClient $sourceClient;

    protected function setUp(): void
    {
        parent::setUp();

        $sourceFactory = new SourceFactory();

        $this->sourceClient = new SourceClient(
            $this->requestFactory,
            $this->serviceClient,
            $sourceFactory,
            $this->exceptionFactory
        );
    }
}
