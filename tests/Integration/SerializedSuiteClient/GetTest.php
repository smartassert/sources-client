<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SerializedSuiteClient;

use PHPUnit\Framework\Attributes\DataProvider;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\Tests\DataProvider\GetSuiteDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;
use Symfony\Component\Uid\Ulid;

class GetTest extends AbstractIntegrationTestCase
{
    use GetSuiteDataProviderTrait;

    public function testGetSerializedSuiteNotFound(): void
    {
        try {
            self::$serializedSuiteClient->get(self::$user1ApiToken, md5((string) rand()));
        } catch (\Throwable $e) {
            self::assertInstanceOf(NonSuccessResponseException::class, $e);
            self::assertSame(404, $e->getCode());
        }
    }

    /**
     * @param callable(): ?non-empty-string             $sourceCreator
     * @param array<non-empty-string, non-empty-string> $parameters
     * @param array<non-empty-string, non-empty-string> $expectedSerializedSuiteParameters
     */
    #[DataProvider('getSuiteDataProvider')]
    public function testGetSuccess(
        callable $sourceCreator,
        array $parameters,
        array $expectedSerializedSuiteParameters,
    ): void {
        $sourceId = $sourceCreator();
        \assert(null !== $sourceId);

        $suiteId = self::$suiteClient->create(
            self::$user1ApiToken,
            $sourceId,
            md5((string) rand()),
            ['Test1.yaml', 'Test2.yaml']
        );
        \assert(null !== $suiteId);

        $serializedSuiteId = (string) new Ulid();
        \assert('' !== $serializedSuiteId);

        $createdSerializedSuite = self::$serializedSuiteClient->create(
            self::$user1ApiToken,
            $serializedSuiteId,
            $suiteId,
            $parameters
        );

        $serializedSuite = self::$serializedSuiteClient->get(
            self::$user1ApiToken,
            $createdSerializedSuite->getId()
        );

        self::assertNotNull($serializedSuite->getId());
        self::assertSame($suiteId, $serializedSuite->getSuiteId());
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
