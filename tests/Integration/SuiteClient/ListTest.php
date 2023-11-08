<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SuiteClient;

use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class ListTest extends AbstractIntegrationTestCase
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
     *   array{'label':non-empty-string, 'source_label': non-empty-string, 'tests': non-empty-string[]}
     * } $suitesData
     */
    public function testListSuccess(array $suitesData): void
    {
        $suites = self::$suiteClient->list(self::$user1ApiToken->token);
        self::assertSame([], $suites);

        $expectedLabels = [];
        foreach ($suitesData as $suiteData) {
            $expectedLabels[] = $suiteData['label'];

            $source = self::$fileSourceClient->create(self::$user1ApiToken->token, $suiteData['source_label']);

            self::$suiteClient->create(
                self::$user1ApiToken->token,
                $source->getId(),
                $suiteData['label'],
                $suiteData['tests']
            );
        }

        $suites = self::$suiteClient->list(self::$user1ApiToken->token);
        self::assertCount(count($suitesData), $suites);

        $suiteLabels = [];
        foreach ($suites as $suite) {
            $suiteLabels[] = $suite->getLabel();
        }

        self::assertSame($expectedLabels, $suiteLabels);
    }

    /**
     * @return array<mixed>
     */
    public static function listSuccessDataProvider(): array
    {
        $suitesData = [
            [
                'source_label' => 'source1',
                'label' => md5((string) rand()),
                'tests' => [],
            ],
            [
                'source_label' => 'source2',
                'label' => md5((string) rand()),
                'tests' => [],
            ],
            [
                'source_label' => 'source1',
                'label' => md5((string) rand()),
                'tests' => [],
            ],
            [
                'source_label' => 'source1',
                'label' => md5((string) rand()),
                'tests' => [],
            ],
            [
                'source_label' => 'source3',
                'label' => md5((string) rand()),
                'tests' => [],
            ],
        ];

        return [
            'no suites' => [
                'suitesData' => [],
            ],
            '0th suite only' => [
                'suitesData' => array_slice($suitesData, 0, 1),
            ],
            '1st to 3rd suites' => [
                'suitesData' => array_slice($suitesData, 1, 3),
            ],
            'all suites' => [
                'suitesData' => $suitesData,
            ],
        ];
    }
}
