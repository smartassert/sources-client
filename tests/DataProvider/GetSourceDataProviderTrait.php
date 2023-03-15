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
                    return self::$sourceClient->createGitSource(
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
                    return self::$sourceClient->createGitSource(
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
                    return self::$sourceClient->createFileSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );
                },
            ],
            'file source, deleted' => [
                'creator' => function () {
                    $createSource = self::$sourceClient->createFileSource(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );

                    return self::$sourceClient->delete(
                        self::$user1ApiToken->token,
                        $createSource->getId()
                    );
                },
            ],
        ];
    }
}
