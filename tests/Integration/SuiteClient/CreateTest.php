<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SuiteClient;

use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\Exception\InvalidRequestException;
use SmartAssert\SourcesClient\Model\InvalidRequestField;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\DataProvider\CreateUpdateSuiteDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class CreateTest extends AbstractIntegrationTestCase
{
    use CreateUpdateSuiteDataProviderTrait;

    /**
     * @dataProvider createUpdateSuiteInvalidRequestDataProvider
     *
     * @param non-empty-string   $label
     * @param non-empty-string[] $tests
     */
    public function testCreateInvalidRequest(string $label, array $tests, InvalidRequestField $expected): void
    {
        $source = self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand()));
        \assert($source instanceof SourceInterface);

        try {
            self::$suiteClient->create(self::$user1ApiToken->token, $source->getId(), $label, $tests);
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidRequestException::class, $e);
            self::assertEquals($expected, $e->getInvalidRequestField());
        }
    }

    public function testCreateSourceNotFound(): void
    {
        $label = md5((string) rand());
        $tests = [];

        try {
            self::$suiteClient->create(self::$user1ApiToken->token, md5((string) rand()), $label, $tests);
        } catch (\Throwable $e) {
            self::assertInstanceOf(NonSuccessResponseException::class, $e);
            self::assertSame(404, $e->getCode());
        }
    }

    /**
     * @dataProvider createLabelNotUniqueDataProvider
     *
     * @param callable(): SourceInterface[] $sourcesCreator
     * @param non-empty-string[]            $firstRequestTests
     * @param non-empty-string[]            $secondRequestTests
     */
    public function testCreateLabelNotUnique(
        callable $sourcesCreator,
        array $firstRequestTests,
        array $secondRequestTests,
    ): void {
        $sources = $sourcesCreator();
        \assert(is_array($sources));
        \assert($sources[0] instanceof SourceInterface);
        \assert($sources[1] instanceof SourceInterface);

        $label = md5((string) rand());

        try {
            self::$suiteClient->create(self::$user1ApiToken->token, $sources[0]->getId(), $label, $firstRequestTests);
            self::$suiteClient->create(self::$user1ApiToken->token, $sources[1]->getId(), $label, $secondRequestTests);
        } catch (InvalidRequestException $e) {
            self::assertInstanceOf(InvalidRequestException::class, $e);
            self::assertEquals(
                new InvalidRequestField(
                    'label',
                    $label,
                    'This label is being used by another suite belonging to this user'
                ),
                $e->getInvalidRequestField()
            );
        }
    }

    /**
     * @return array<mixed>
     */
    public static function createLabelNotUniqueDataProvider(): array
    {
        return [
            'same source, different tests' => [
                'sourcesCreator' => function () {
                    $source = self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand()));

                    return [$source, $source];
                },
                'firstRequestTests' => [],
                'secondRequestTests' => ['test.yaml'],
            ],
            'different source, same tests' => [
                'sourcesCreator' => function () {
                    return [
                        self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand())),
                        self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand()))
                    ];
                },
                'firstRequestTests' => ['test.yaml'],
                'secondRequestTests' => ['test.yaml'],
            ],
            'different source, different tests' => [
                'sourcesCreator' => function () {
                    return [
                        self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand())),
                        self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand()))
                    ];
                },
                'firstRequestTests' => ['test1.yaml'],
                'secondRequestTests' => ['test2.yaml'],
            ],
        ];
    }

    /**
     * @dataProvider createUpdateSuiteSuccessDataProvider
     *
     * @param non-empty-string   $label
     * @param non-empty-string[] $tests
     */
    public function testCreateSuccess(string $label, array $tests): void
    {
        $source = self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand()));
        \assert($source instanceof SourceInterface);

        $suite = self::$suiteClient->create(self::$user1ApiToken->token, $source->getId(), $label, $tests);

        self::assertNotEmpty($suite->getId());
        self::assertSame($source->getId(), $suite->getSourceId());
        self::assertSame($label, $suite->getLabel());
        self::assertSame($tests, $suite->getTests());
        self::assertNull($suite->getDeletedAt());
    }
}
