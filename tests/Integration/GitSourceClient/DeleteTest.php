<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\GitSourceClient;

use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\DataProvider\GetGitSourceDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class DeleteTest extends AbstractIntegrationTestCase
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
    public function testDeleteSuccess(callable $creator): void
    {
        $createdSource = $creator();
        $retrievedSource = self::$gitSourceClient->delete(self::$user1ApiToken->token, $createdSource->getId());

        self::assertSame($createdSource->getId(), $retrievedSource->getId());
        self::assertNotNull($retrievedSource->getDeletedAt());
    }
}
