<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\FileSourceClient;

use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\DataProvider\GetFileSourceDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class GetTest extends AbstractIntegrationTestCase
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
    public function testGetSuccess(callable $creator): void
    {
        $createdSource = $creator();
        $retrievedSource = self::$fileSourceClient->get(self::$user1ApiToken->token, $createdSource->getId());

        self::assertEquals($createdSource, $retrievedSource);
    }
}
