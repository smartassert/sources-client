<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\SerializedSuiteClient;

use SmartAssert\SourcesClient\SerializedSuiteClient;
use SmartAssert\SourcesClient\SerializedSuiteFactory;
use SmartAssert\SourcesClient\Tests\Functional\Client\AbstractClientTestCase;

abstract class AbstractSuiteClientTestCase extends AbstractClientTestCase
{
    protected SerializedSuiteClient $serializedSuiteClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializedSuiteClient = new SerializedSuiteClient(
            $this->requestFactory,
            $this->serviceClient,
            new SerializedSuiteFactory(),
            $this->exceptionFactory
        );
    }
}
