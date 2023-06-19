<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SerializedSuiteClient;

use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\DataProvider\GetSuiteDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class GetTest extends AbstractIntegrationTestCase
{
    use GetSuiteDataProviderTrait;

    public function testGetSerializedSuiteNotFound(): void
    {
        try {
            self::$serializedSuiteClient->get(self::$user1ApiToken->token, md5((string) rand()));
        } catch (\Throwable $e) {
            self::assertInstanceOf(NonSuccessResponseException::class, $e);
            self::assertSame(404, $e->getCode());
        }
    }

    /**
     * @dataProvider getSuiteDataProvider
     *
     * @param callable(): SourceInterface               $sourceCreator
     * @param array<non-empty-string, non-empty-string> $parameters
     * @param array<non-empty-string, non-empty-string> $expectedSerializedSuiteParameters
     */
    public function testGetSuccess(
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

        $createdSerializedSuite = self::$serializedSuiteClient->create(
            self::$user1ApiToken->token,
            $suite->getId(),
            $parameters
        );

        $serializedSuite = self::$serializedSuiteClient->get(
            self::$user1ApiToken->token,
            $createdSerializedSuite->getId()
        );

        self::assertNotNull($serializedSuite->getId());
        self::assertSame($suite->getId(), $serializedSuite->getSuiteId());
        self::assertSame($expectedSerializedSuiteParameters, $serializedSuite->getParameters());
        self::assertNull($serializedSuite->getFailureReason());
        self::assertNull($serializedSuite->getFailureMessage());

        $expectedStates = ['requested', 'preparing/running', 'preparing/halted', 'prepared'];

        self::assertTrue(
            in_array($serializedSuite->getState(), $expectedStates),
            sprintf(
                'Serialized suite state "%s" is not one of %s',
                $serializedSuite->getState(),
                implode(', ', $expectedStates)
            )
        );
    }
}
