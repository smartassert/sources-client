<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Exception\InvalidRequestException;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\InvalidRequestField;
use SmartAssert\SourcesClient\Tests\DataProvider\CreateUpdateFileSourceDataProviderTrait;

class CreateFileSourceTest extends AbstractIntegrationTestCase
{
    use CreateUpdateFileSourceDataProviderTrait;

    /**
     * @dataProvider createUpdateFileSourceInvalidRequestDataProvider
     *
     * @param non-empty-string $label
     */
    public function testCreateFileSourceInvalidRequest(string $label, InvalidRequestField $expected): void
    {
        try {
            self::$client->sourceHandler->createFileSource(self::$user1ApiToken->token, $label);
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidRequestException::class, $e);
            self::assertEquals($expected, $e->getInvalidRequestField());
        }
    }

    public function testCreateFileSourceSuccess(): void
    {
        $label = md5((string) rand());

        $fileSource = self::$client->sourceHandler->createFileSource(self::$user1ApiToken->token, $label);

        self::assertInstanceOf(FileSource::class, $fileSource);
        self::assertSame($label, $fileSource->getLabel());
        self::assertNotEmpty($fileSource->getId());
    }
}
