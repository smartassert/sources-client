<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\GitSourceClient;

use SmartAssert\SourcesClient\GitSourceClient;
use SmartAssert\SourcesClient\SourceFactory;
use SmartAssert\SourcesClient\Tests\Functional\Client\AbstractClientTestCase;

abstract class AbstractGitSourceClientTestCase extends AbstractClientTestCase
{
    protected GitSourceClient $gitSourceClient;

    protected function setUp(): void
    {
        parent::setUp();

        $sourceFactory = new SourceFactory();

        $this->gitSourceClient = new GitSourceClient(
            $this->serviceClient,
            $sourceFactory,
            $this->exceptionFactory,
            'https://sources.example.com'
        );
    }
}
