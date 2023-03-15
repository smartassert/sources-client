<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SourceClient;

use SmartAssert\SourcesClient\Exception\InvalidRequestException;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\InvalidRequestField;
use SmartAssert\SourcesClient\Tests\DataProvider\CreateUpdateFileSourceDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class UpdateFileSourceTest extends AbstractIntegrationTestCase
{
    use CreateUpdateFileSourceDataProviderTrait;

    private FileSource $fileSource;

    protected function setUp(): void
    {
        parent::setUp();

        $label = md5((string) rand());

        $fileSource = self::$sourceClient->createFileSource(self::$user1ApiToken->token, $label);
        \assert($fileSource instanceof FileSource);
        $this->fileSource = $fileSource;
    }

    /**
     * @dataProvider createUpdateFileSourceInvalidRequestDataProvider
     *
     * @param non-empty-string $label
     */
    public function testUpdateFileSourceInvalidRequest(string $label, InvalidRequestField $expected): void
    {
        try {
            self::$sourceClient->updateFileSource(
                self::$user1ApiToken->token,
                $this->fileSource->getId(),
                $label
            );
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidRequestException::class, $e);
            self::assertEquals($expected, $e->getInvalidRequestField());
        }
    }

    public function testUpdateFileSourceSuccess(): void
    {
        $label = md5((string) rand());

        $fileSource = self::$sourceClient->updateFileSource(
            self::$user1ApiToken->token,
            $this->fileSource->getId(),
            $label
        );

        self::assertInstanceOf(FileSource::class, $fileSource);
        self::assertSame($label, $fileSource->getLabel());
        self::assertNotEmpty($fileSource->getId());
    }
}
