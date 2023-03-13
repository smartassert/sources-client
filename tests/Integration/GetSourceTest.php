<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Model\ErrorInterface;
use SmartAssert\SourcesClient\Model\SourceInterface;

class GetSourceTest extends AbstractIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::$dataRepository->removeAllData();
    }

    /**
     * @dataProvider getSourceSuccessDataProvider
     *
     * @param callable(): SourceInterface $creator
     */
    public function testGetSourceSuccess(callable $creator): void
    {
        $createdSource = $creator();
        $retrievedSource = self::$client->getSource(self::$user1ApiToken->token, $createdSource->getId());

        self::assertEquals($createdSource, $retrievedSource);
    }

    /**
     * @return array<mixed>
     */
    public function getSourceSuccessDataProvider(): array
    {
        return [
            'git source without credentials' => [
                'creator' => function () {
                    $source = self::$client->createGitSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                        md5((string) rand()),
                        md5((string) rand()),
                        null,
                    );

                    if ($source instanceof ErrorInterface) {
                        throw new \RuntimeException('Failed to create git source');
                    }

                    return $source;
                },
            ],
            'git source with credentials' => [
                'creator' => function () {
                    $source = self::$client->createGitSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                        md5((string) rand()),
                        md5((string) rand()),
                        md5((string) rand()),
                    );

                    if ($source instanceof ErrorInterface) {
                        throw new \RuntimeException('Failed to create source');
                    }

                    return $source;
                },
            ],
            'file source' => [
                'creator' => function () {
                    $source = self::$client->createFileSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );

                    if ($source instanceof ErrorInterface) {
                        throw new \RuntimeException('Failed to create source');
                    }

                    return $source;
                },
            ],
        ];
    }
}
