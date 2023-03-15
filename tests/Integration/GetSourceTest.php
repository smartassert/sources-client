<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\DataProvider\GetSourceDataProviderTrait;

class GetSourceTest extends AbstractIntegrationTestCase
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
    public function testGetSourceSuccess(callable $creator): void
    {
        $createdSource = $creator();
        $retrievedSource = self::$client->sourceClient->get(self::$user1ApiToken->token, $createdSource->getId());

        self::assertEquals($createdSource, $retrievedSource);
    }
}
