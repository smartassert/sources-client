<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\DataProvider\GetSourceDataProviderTrait;

class DeleteSourceTest extends AbstractIntegrationTestCase
{
    use GetSourceDataProviderTrait;

    protected function setUp(): void
    {
        parent::setUp();

        self::$dataRepository->removeAllData();
    }

    /**
     * @dataProvider getSourceDataProvider
     *
     * @param callable(): SourceInterface $creator
     */
    public function testDeleteSourceSuccess(callable $creator): void
    {
        $createdSource = $creator();
        $retrievedSource = self::$client->sourceClient->delete(self::$user1ApiToken->token, $createdSource->getId());

        self::assertSame($createdSource->getId(), $retrievedSource->getId());
        self::assertNotNull($retrievedSource->getDeletedAt());
    }
}
