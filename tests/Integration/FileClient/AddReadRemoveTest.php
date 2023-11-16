<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\FileClient;

use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\Exception\DuplicateFilePathException;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class AddReadRemoveTest extends AbstractIntegrationTestCase
{
    public function testAddDuplicateFilePath(): void
    {
        $label = md5((string) rand());

        $fileSource = self::$fileSourceClient->create(self::$user1ApiToken->token, $label);
        self::assertInstanceOf(FileSource::class, $fileSource);

        $filename = md5((string) rand()) . '.yaml';
        $content = md5((string) rand());

        self::$fileClient->add(self::$user1ApiToken->token, $fileSource->getId(), $filename, $content);

        self::expectException(DuplicateFilePathException::class);
        self::$fileClient->add(self::$user1ApiToken->token, $fileSource->getId(), $filename, $content);
    }

    public function testAddFileReadFile(): void
    {
        $label = md5((string) rand());

        $fileSource = self::$fileSourceClient->create(self::$user1ApiToken->token, $label);
        self::assertInstanceOf(FileSource::class, $fileSource);

        $filename = md5((string) rand()) . '.yaml';
        $content = md5((string) rand());

        self::$fileClient->add(
            self::$user1ApiToken->token,
            $fileSource->getId(),
            $filename,
            $content
        );

        $readFileResponse = self::$fileClient->read(
            self::$user1ApiToken->token,
            $fileSource->getId(),
            $filename
        );
        self::assertSame($content, $readFileResponse);

        self::$fileClient->remove(self::$user1ApiToken->token, $fileSource->getId(), $filename);

        self::expectException(NonSuccessResponseException::class);
        self::expectExceptionCode(404);
        self::$fileClient->read(self::$user1ApiToken->token, $fileSource->getId(), $filename);
    }
}
