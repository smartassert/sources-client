<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SourceClient;

use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\DataProvider\GetSourceDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

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
        $retrievedSource = self::$sourceClient->get(self::$user1ApiToken->token, $createdSource->getId());

        self::assertEquals($createdSource, $retrievedSource);
    }
}
