<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

class ListSourcesTest extends AbstractIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::$dataRepository->removeAllData();
    }

    /**
     * @dataProvider listSuccessDataProvider
     *
     * @param array{
     *   array{'type':non-empty-string, 'label':non-empty-string}
     * } $sourcesData
     */
    public function testListSourcesSuccess(array $sourcesData): void
    {
        $sources = self::$client->listSources(self::$user1ApiToken->token);
        self::assertSame([], $sources);

        $expectedLabels = [];
        foreach ($sourcesData as $sourceData) {
            $expectedLabels[] = $sourceData['label'];

            if ('file' === $sourceData['type']) {
                self::$client->sourceHandler->createFileSource(self::$user1ApiToken->token, $sourceData['label']);
            }

            if ('git' === $sourceData['type']) {
                self::$client->sourceHandler->createGitSource(
                    self::$user1ApiToken->token,
                    $sourceData['label'],
                    md5((string) rand()),
                    md5((string) rand()),
                    null
                );
            }
        }

        $sources = self::$client->listSources(self::$user1ApiToken->token);
        self::assertCount(count($sourcesData), $sources);

        $sourceLabels = [];
        foreach ($sources as $source) {
            $sourceLabels[] = $source->getLabel();
        }

        self::assertSame($expectedLabels, $sourceLabels);
    }

    /**
     * @return array<mixed>
     */
    public function listSuccessDataProvider(): array
    {
        $sourcesData = [
            [
                'type' => 'file',
                'label' => md5((string) rand()),
            ],
            [
                'type' => 'file',
                'label' => md5((string) rand()),
            ],
            [
                'type' => 'git',
                'label' => md5((string) rand()),
            ],
            [
                'type' => 'file',
                'label' => md5((string) rand()),
            ],
            [
                'type' => 'git',
                'label' => md5((string) rand()),
            ],
        ];

        return [
            'no sources' => [
                'sourcesData' => [],
            ],
            '0th source only' => [
                'sourcesData' => array_slice($sourcesData, 0, 1),
            ],
            '1st to 3rd sources' => [
                'sourcesData' => array_slice($sourcesData, 1, 3),
            ],
            'all sources' => [
                'sourcesData' => $sourcesData,
            ],
        ];
    }
}
