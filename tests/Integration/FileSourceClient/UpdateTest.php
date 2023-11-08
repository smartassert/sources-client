<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\FileSourceClient;

use SmartAssert\SourcesClient\Exception\InvalidRequestException;
use SmartAssert\SourcesClient\Exception\ModifyReadOnlyEntityException;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\InvalidRequestField;
use SmartAssert\SourcesClient\Tests\DataProvider\CreateUpdateFileSourceDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class UpdateTest extends AbstractIntegrationTestCase
{
    use CreateUpdateFileSourceDataProviderTrait;

    private FileSource $fileSource;

    protected function setUp(): void
    {
        parent::setUp();

        $label = md5((string) rand());

        $fileSource = self::$fileSourceClient->create(self::$user1ApiToken->token, $label);
        \assert($fileSource instanceof FileSource);
        $this->fileSource = $fileSource;
    }

    /**
     * @dataProvider createUpdateFileSourceInvalidRequestDataProvider
     *
     * @param non-empty-string $label
     */
    public function testUpdateInvalidRequest(string $label, InvalidRequestField $expected): void
    {
        try {
            self::$fileSourceClient->update(
                self::$user1ApiToken->token,
                $this->fileSource->getId(),
                $label
            );
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidRequestException::class, $e);
            self::assertEquals($expected, $e->getInvalidRequestField());
        }
    }

    public function testUpdateSuccess(): void
    {
        $label = md5((string) rand());

        $fileSource = self::$fileSourceClient->update(
            self::$user1ApiToken->token,
            $this->fileSource->getId(),
            $label
        );

        self::assertInstanceOf(FileSource::class, $fileSource);
        self::assertSame($label, $fileSource->getLabel());
        self::assertNotEmpty($fileSource->getId());
    }

    public function testUpdateDeletedFileSource(): void
    {
        self::$fileSourceClient->delete(self::$user1ApiToken->token, $this->fileSource->getId());

        self::expectException(ModifyReadOnlyEntityException::class);
        self::expectExceptionMessage('Cannot modify read-only source ' . $this->fileSource->getId() . '.');

        self::$fileSourceClient->update(
            self::$user1ApiToken->token,
            $this->fileSource->getId(),
            md5((string) rand())
        );
    }
}
