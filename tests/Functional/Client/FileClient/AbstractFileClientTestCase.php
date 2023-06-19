<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\FileClient;

use SmartAssert\SourcesClient\FileClient;
use SmartAssert\SourcesClient\Tests\Functional\Client\AbstractClientTestCase;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

abstract class AbstractFileClientTestCase extends AbstractClientTestCase
{
    use NetworkErrorExceptionDataProviderTrait;

    protected FileClient $fileClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileClient = new FileClient($this->requestFactory, $this->serviceClient, $this->exceptionFactory);
    }
}
