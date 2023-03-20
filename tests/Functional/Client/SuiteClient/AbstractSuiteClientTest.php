<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\SuiteClient;

use SmartAssert\SourcesClient\SuiteClient;
use SmartAssert\SourcesClient\SuiteFactory;
use SmartAssert\SourcesClient\Tests\Functional\Client\AbstractClientTestCase;

abstract class AbstractSuiteClientTest extends AbstractClientTestCase
{
    protected SuiteClient $suiteClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->suiteClient = new SuiteClient(
            $this->requestFactory,
            $this->serviceClient,
            new SuiteFactory(),
            $this->exceptionFactory
        );
    }
}