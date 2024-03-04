<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SerializedSuiteClient;

use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\DataProvider\GetSuiteDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;
use Symfony\Component\Uid\Ulid;

class CreateTest extends AbstractIntegrationTestCase
{
    use GetSuiteDataProviderTrait;

    public function testCreateSuiteNotFound(): void
    {
        try {
            self::$serializedSuiteClient->create(
                self::$user1ApiToken->token,
                md5((string) rand()),
                md5((string) rand())
            );
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
    public function testCreateSuccess(
        callable $sourceCreator,
        array $parameters,
        array $expectedSerializedSuiteParameters,
    ): void {
        $source = $sourceCreator();

        $suiteId = self::$suiteClient->create(
            self::$user1ApiToken->token,
            $source->getId(),
            md5((string) rand()),
            ['Test1.yaml', 'Test2.yaml']
        );
        \assert(null !== $suiteId);

        $serializedSuiteId = (string) new Ulid();
        \assert('' !== $serializedSuiteId);

        $serializedSuite = self::$serializedSuiteClient->create(
            self::$user1ApiToken->token,
            $serializedSuiteId,
            $suiteId,
            $parameters
        );

        self::assertNotNull($serializedSuite->getId());
        self::assertSame($suiteId, $serializedSuite->getSuiteId());
        self::assertSame($expectedSerializedSuiteParameters, $serializedSuite->getParameters());
        self::assertSame('requested', $serializedSuite->getState());
        self::assertNull($serializedSuite->getFailureReason());
        self::assertNull($serializedSuite->getFailureMessage());
    }
}
