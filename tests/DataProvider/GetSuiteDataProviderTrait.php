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
            return self::$fileSourceClient->create(self::$user1ApiToken, md5((string) rand()));
        };

        $gitSourceCreator = function () {
            return self::$gitSourceClient->create(
                self::$user1ApiToken,
                md5((string) rand()),
                'https://example.com/' . md5((string) rand()) . '.git',
                '/',
                null
            );
        };

        return [
            'file source, no parameters, no notify url' => [
                'sourceCreator' => $fileSourceCreator,
                'notifyUrl' => null,
                'parameters' => [],
                'expectedSerializedSuiteParameters' => [],
            ],
            'file source, no parameters' => [
                'sourceCreator' => $fileSourceCreator,
                'notifyUrl' => 'https://example.com/notify',
                'parameters' => [],
                'expectedSerializedSuiteParameters' => [],
            ],
            'file source, has parameters' => [
                'sourceCreator' => $fileSourceCreator,
                'notifyUrl' => 'https://example.com/notify',
                'parameters' => [
                    'foo1' => 'bar1',
                    'foo2' => 'bar2',
                    'ref' => 'hash value',
                ],
                'expectedSerializedSuiteParameters' => [],
            ],
            'git source, no parameters' => [
                'sourceCreator' => $gitSourceCreator,
                'notifyUrl' => 'https://example.com/notify',
                'parameters' => [],
                'expectedSerializedSuiteParameters' => [],
            ],
            'git source, has parameters' => [
                'sourceCreator' => $gitSourceCreator,
                'notifyUrl' => 'https://example.com/notify',
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
