<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\DataProvider;

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
                    return self::$client->sourceHandler->createGitSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                        md5((string) rand()),
                        md5((string) rand()),
                        null,
                    );
                },
            ],
            'git source with credentials' => [
                'creator' => function () {
                    return self::$client->sourceHandler->createGitSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                        md5((string) rand()),
                        md5((string) rand()),
                        md5((string) rand()),
                    );
                },
            ],
            'file source' => [
                'creator' => function () {
                    return self::$client->sourceHandler->createFileSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );
                },
            ],
            'file source, deleted' => [
                'creator' => function () {
                    $createSource = self::$client->sourceHandler->createFileSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );

                    return self::$client->deleteSource(
                        self::$user1ApiToken->token,
                        $createSource->getId()
                    );
                },
            ],
        ];
    }
}
