<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\FileSourceClient;

use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\DataProvider\GetFileSourceDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class DeleteTest extends AbstractIntegrationTestCase
{
    use GetFileSourceDataProviderTrait;

    protected function setUp(): void
    {
        parent::setUp();

        self::$dataRepository->removeAllData();
    }

    /**
     * @dataProvider getFileSourceDataProvider
     *
     * @param callable(): SourceInterface $creator
     */
    public function testDeleteSourceSuccess(callable $creator): void
    {
        $createdSource = $creator();
        $retrievedSource = self::$fileSourceClient->delete(self::$user1ApiToken->token, $createdSource->getId());

        self::assertSame($createdSource->getId(), $retrievedSource->getId());
        self::assertNotNull($retrievedSource->getDeletedAt());
    }
}
