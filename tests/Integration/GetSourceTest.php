<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Model\ErrorInterface;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\RunSource;
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
                'creator' => function (): GitSource {
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
                'creator' => function (): GitSource {
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
                'creator' => function (): FileSource {
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
            'run source with parent without parameters' => [
                'creator' => function (): RunSource {
                    $fileSource = self::$client->createFileSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );

                    if ($fileSource instanceof ErrorInterface) {
                        throw new \RuntimeException('Failed to create source');
                    }

                    return self::$sourceEntityFactory->createRunSourceFromOriginSource(
                        $fileSource,
                        [],
                        'prepared',
                    );
                },
            ],
            'run source with parent with parameters' => [
                'creator' => function (): RunSource {
                    $fileSource = self::$client->createFileSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );

                    if ($fileSource instanceof ErrorInterface) {
                        throw new \RuntimeException('Failed to create source');
                    }

                    return self::$sourceEntityFactory->createRunSourceFromOriginSource(
                        $fileSource,
                        [
                            'param_1_key' => 'param_1_value',
                            'param_2_key' => 'param_2_value',
                        ],
                        'prepared',
                    );
                },
            ],
            'run source without parent' => [
                'creator' => function (): RunSource {
                    $fileSource = self::$client->createFileSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );

                    if ($fileSource instanceof ErrorInterface) {
                        throw new \RuntimeException('Failed to create source');
                    }

                    return self::$sourceEntityFactory->createRunSourceWithoutParent(
                        $fileSource->getUserId(),
                        [],
                        'requested',
                    );
                },
            ],
            'run source with failure reason and failure message' => [
                'creator' => function (): RunSource {
                    $fileSource = self::$client->createFileSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );

                    if ($fileSource instanceof ErrorInterface) {
                        throw new \RuntimeException('Failed to create source');
                    }

                    return self::$sourceEntityFactory->createRunSourceFromOriginSource(
                        $fileSource,
                        [],
                        'failed',
                        'git/clone',
                        'fatal: repository \'https://example.com/repository.git\' not found'
                    );
                },
            ],
        ];
    }
}
