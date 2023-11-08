<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\DataProvider;

trait GetSuiteDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public static function getSuiteDataProvider(): array
    {
        $fileSourceCreator = function () {
            return self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand()));
        };

        $gitSourceCreator = function () {
            return self::$gitSourceClient->create(
                self::$user1ApiToken->token,
                md5((string) rand()),
                'https://example.com/' . md5((string) rand()) . '.git',
                '/',
                null
            );
        };

        return [
            'file source, no parameters' => [
                'sourceCreator' => $fileSourceCreator,
                'parameters' => [],
                'expectedSerializedSuiteParameters' => [],
            ],
            'file source, has parameters' => [
                'sourceCreator' => $fileSourceCreator,
                'parameters' => [
                    'foo1' => 'bar1',
                    'foo2' => 'bar2',
                    'ref' => 'hash value',
                ],
                'expectedSerializedSuiteParameters' => [],
            ],
            'git source, no parameters' => [
                'sourceCreator' => $gitSourceCreator,
                'parameters' => [],
                'expectedSerializedSuiteParameters' => [],
            ],
            'git source, has parameters' => [
                'sourceCreator' => $gitSourceCreator,
                'parameters' => [
                    'foo1' => 'bar1',
                    'foo2' => 'bar2',
                    'ref' => 'hash value',
                ],
                'expectedSerializedSuiteParameters' => ['ref' => 'hash value'],
            ],
        ];
    }
}
