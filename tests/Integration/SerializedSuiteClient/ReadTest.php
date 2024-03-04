<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SerializedSuiteClient;

use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\SerializedSuite;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;
use Symfony\Component\Uid\Ulid;

class ReadTest extends AbstractIntegrationTestCase
{
    public function testReadSerializedSuiteNotFound(): void
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

    public function testReadSuccess(): void
    {
        $source = self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand()));
        \assert($source instanceof FileSource);

        $sourcePaths = [
            'Source/File.yaml',
            'Source/Page.yaml',
            'Source/Test1.yaml',
            'Source/Test2.yaml',
        ];

        foreach ($sourcePaths as $sourcePath) {
            self::$fileClient->add(
                self::$user1ApiToken->token,
                $source->getId(),
                $sourcePath,
                self::$fixtureReader->read($sourcePath)
            );
        }

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
        );

        $this->waitUntilSuiteIsSerialized($serializedSuite);

        $expectedSerializedSuiteContent = trim(self::$fixtureReader->read('SerializedSuite/suite.yaml'));
        $serializedSuiteContent = self::$serializedSuiteClient->read(
            self::$user1ApiToken->token,
            $serializedSuite->getId()
        );

        self::assertSame($expectedSerializedSuiteContent, $serializedSuiteContent);
    }

    private function waitUntilSuiteIsSerialized(SerializedSuite $serializedSuite): void
    {
        $timeout = 30000;
        $duration = 0;
        $period = 1000;

        while ('prepared' !== $serializedSuite->getState()) {
            $serializedSuite = self::$serializedSuiteClient->get(
                self::$user1ApiToken->token,
                $serializedSuite->getId(),
            );

            if ('prepared' !== $serializedSuite->getState()) {
                $duration += $period;

                if ($duration >= $timeout) {
                    throw new \RuntimeException('Timed out waiting for "' . $serializedSuite->getId() . '" to prepare');
                }

                usleep($period);
            }
        }
    }
}
