<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SuiteClient;

use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class DeleteTest extends AbstractIntegrationTestCase
{
    public function testDeleteSuccess(): void
    {
        $source = self::$sourceClient->createFileSource(self::$user1ApiToken->token, md5((string) rand()));
        \assert($source instanceof SourceInterface);

        $label = md5((string) rand());
        $tests = [md5((string) rand()) . '.yaml'];

        $createdSuite = self::$suiteClient->create(self::$user1ApiToken->token, $source->getId(), $label, $tests);
        $deletedSuite = self::$suiteClient->delete(self::$user1ApiToken->token, $createdSuite->getId());

        self::assertSame($createdSuite->getId(), $deletedSuite->getId());
        self::assertSame($createdSuite->getSourceId(), $deletedSuite->getSourceId());
        self::assertSame($createdSuite->getLabel(), $deletedSuite->getLabel());
        self::assertSame($createdSuite->getTests(), $deletedSuite->getTests());
        self::assertNotNull($deletedSuite->getDeletedAt());
    }
}
