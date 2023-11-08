<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\GitSourceClient;

use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\DataProvider\GetGitSourceDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class GetTest extends AbstractIntegrationTestCase
{
    use GetGitSourceDataProviderTrait;

    protected function setUp(): void
    {
        parent::setUp();

        self::$dataRepository->removeAllData();
    }

    /**
     * @dataProvider getGitSourceDataProvider
     *
     * @param callable(): SourceInterface $creator
     */
    public function testGetSuccess(callable $creator): void
    {
        $createdSource = $creator();
        $retrievedSource = self::$gitSourceClient->get(self::$user1ApiToken->token, $createdSource->getId());

        self::assertEquals($createdSource, $retrievedSource);
    }
}
