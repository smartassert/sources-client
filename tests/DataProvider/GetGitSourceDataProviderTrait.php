<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\DataProvider;

trait GetGitSourceDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public static function getGitSourceDataProvider(): array
    {
        return [
            'git source without credentials' => [
                'creator' => function () {
                    return self::$gitSourceClient->create(
                        self::$user1ApiToken,
                        md5((string) rand()),
                        md5((string) rand()),
                        md5((string) rand()),
                        null,
                    );
                },
            ],
            'git source with credentials' => [
                'creator' => function () {
                    return self::$gitSourceClient->create(
                        self::$user1ApiToken,
                        md5((string) rand()),
                        md5((string) rand()),
                        md5((string) rand()),
                        md5((string) rand()),
                    );
                },
            ],
        ];
    }
}
