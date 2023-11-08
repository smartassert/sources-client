<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\DataProvider;

trait GetFileSourceDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public static function getFileSourceDataProvider(): array
    {
        return [
            'file source' => [
                'creator' => function () {
                    return self::$fileSourceClient->create(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );
                },
            ],
            'file source, deleted' => [
                'creator' => function () {
                    $createSource = self::$fileSourceClient->create(
                        self::$user1ApiToken->token,
                        md5((string) rand()),
                    );

                    return self::$fileSourceClient->delete(
                        self::$user1ApiToken->token,
                        $createSource->getId()
                    );
                },
            ],
        ];
    }
}
