<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\DataProvider;

use SmartAssert\SourcesClient\Model\ErrorInterface;

trait GetSourceDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public function getSourceDataProvider(): array
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
            'file source, deleted' => [
                'creator' => function () {
                    $createSource = self::$client->createFileSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );

                    if ($createSource instanceof ErrorInterface) {
                        throw new \RuntimeException('Failed to create source');
                    }

                    return self::$client->deleteSource(
                        self::$user1ApiToken->token,
                        $createSource->getId()
                    );
                },
            ],
        ];
    }
}
