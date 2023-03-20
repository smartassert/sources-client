<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SerializedSuiteClient;

use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class CreateTest extends AbstractIntegrationTestCase
{
    public function testCreateSuiteNotFound(): void
    {
        try {
            self::$serializedSuiteClient->create(self::$user1ApiToken->token, md5((string) rand()));
        } catch (\Throwable $e) {
            self::assertInstanceOf(NonSuccessResponseException::class, $e);
            self::assertSame(404, $e->getCode());
        }
    }

    /**
     * @dataProvider createSuccessDataProvider
     *
     * @param callable(): SourceInterface               $sourceCreator
     * @param array<non-empty-string, non-empty-string> $parameters
     * @param array<non-empty-string, non-empty-string> $expectedSerializedSuiteParameters
     */
    public function testCreateSuccess(
        callable $sourceCreator,
        array $parameters,
        array $expectedSerializedSuiteParameters,
    ): void {
        $source = $sourceCreator();

        $suite = self::$suiteClient->create(
            self::$user1ApiToken->token,
            $source->getId(),
            md5((string) rand()),
            ['Test1.yaml', 'Test2.yaml']
        );

        $serializedSuite = self::$serializedSuiteClient->create(
            self::$user1ApiToken->token,
            $suite->getId(),
            $parameters
        );

        self::assertNotNull($serializedSuite->getId());
        self::assertSame($suite->getId(), $serializedSuite->getSuiteId());
        self::assertSame($expectedSerializedSuiteParameters, $serializedSuite->getParameters());
        self::assertSame('requested', $serializedSuite->getState());
        self::assertNull($serializedSuite->getFailureReason());
        self::assertNull($serializedSuite->getFailureMessage());
    }

    /**
     * @return array<mixed>
     */
    public function createSuccessDataProvider(): array
    {
        $fileSourceCreator = function () {
            return self::$sourceClient->createFileSource(self::$user1ApiToken->token, md5((string) rand()));
        };

        $gitSourceCreator = function () {
            return self::$sourceClient->createGitSource(
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
