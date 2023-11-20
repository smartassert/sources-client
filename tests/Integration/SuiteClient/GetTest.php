<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SuiteClient;

use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class GetTest extends AbstractIntegrationTestCase
{
    public function testGetSuccess(): void
    {
        $source = self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand()));
        \assert($source instanceof SourceInterface);

        $label = md5((string) rand());
        $tests = [md5((string) rand()) . '.yaml'];

        $createdSuite = self::$suiteClient->create(self::$user1ApiToken->token, $source->getId(), $label, $tests);
        $retrievedSuite = self::$suiteClient->get(self::$user1ApiToken->token, $createdSuite->getId());

        self::assertSame($createdSuite->getId(), $retrievedSuite->getId());
        self::assertSame($createdSuite->getSourceId(), $retrievedSuite->getSourceId());
        self::assertSame($createdSuite->getLabel(), $retrievedSuite->getLabel());
        self::assertSame($createdSuite->getTests(), $retrievedSuite->getTests());
        self::assertNotNull($retrievedSuite->getDeletedAt());
    }
}
