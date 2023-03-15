<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

class ListFileSourceFilenamesTest extends AbstractIntegrationTestCase
{
    /**
     * @dataProvider listFileSourceFilenamesDataProvider
     *
     * @param non-empty-string[] $filenamesToAdd
     * @param string[]           $expected
     */
    public function testListFileSourceFilenames(array $filenamesToAdd, array $expected): void
    {
        $fileSource = self::$client->createFileSource(self::$user1ApiToken->token, md5((string) rand()));

        foreach ($filenamesToAdd as $filename) {
            self::$client->fileSourceFileHandler->add(
                self::$user1ApiToken->token,
                $fileSource->getId(),
                $filename,
                md5((string) rand())
            );
        }

        $actual = self::$client->listFileSourceFilenames(self::$user1ApiToken->token, $fileSource->getId());

        self::assertSame($expected, $actual);
    }

    /**
     * @return array<mixed>
     */
    public function listFileSourceFilenamesDataProvider(): array
    {
        return [
            'none' => [
                'filenamesToAdd' => [],
                'expected' => [],
            ],
            'single' => [
                'filenamesToAdd' => [
                    'alpaca.yaml',
                ],
                'expected' => [
                    'alpaca.yaml',
                ],
            ],
            'multiple, added in order' => [
                'filenamesToAdd' => [
                    'alpaca.yaml',
                    'baboon.yaml',
                    'zebra.yaml',
                ],
                'expected' => [
                    'alpaca.yaml',
                    'baboon.yaml',
                    'zebra.yaml',
                ],
            ],
            'multiple, added out of order' => [
                'filenamesToAdd' => [
                    'alpaca.yaml',
                    'zebra.yaml',
                    'baboon.yaml',
                ],
                'expected' => [
                    'alpaca.yaml',
                    'baboon.yaml',
                    'zebra.yaml',
                ],
            ],
        ];
    }
}
