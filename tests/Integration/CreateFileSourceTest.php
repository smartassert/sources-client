<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\InvalidRequestError;
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
        $invalidRequestError = self::$client->createFileSource(self::$user1ApiToken->token, $label);

        self::assertInstanceOf(InvalidRequestError::class, $invalidRequestError);
        self::assertEquals($expected, $invalidRequestError->getInvalidRequestField());
    }

    public function testCreateFileSourceSuccess(): void
    {
        $label = md5((string) rand());

        $fileSource = self::$client->createFileSource(self::$user1ApiToken->token, $label);

        self::assertInstanceOf(FileSource::class, $fileSource);
        self::assertSame($label, $fileSource->getLabel());
        self::assertNotEmpty($fileSource->getId());
    }
}
