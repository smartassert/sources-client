<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\Model\FileSource;

class AddFileReadFileRemoveFileTest extends AbstractIntegrationTestCase
{
    public function testAddFileReadFile(): void
    {
        $label = md5((string) rand());

        $fileSource = self::$client->createFileSource(self::$user1ApiToken->token, $label);
        self::assertInstanceOf(FileSource::class, $fileSource);

        $filename = md5((string) rand()) . '.yaml';
        $content = md5((string) rand());

        self::$client->fileSourceFileHandler->add(
            self::$user1ApiToken->token,
            $fileSource->getId(),
            $filename,
            $content
        );

        $readFileResponse = self::$client->fileSourceFileHandler->read(
            self::$user1ApiToken->token,
            $fileSource->getId(),
            $filename
        );
        self::assertSame($content, $readFileResponse);

        self::$client->fileSourceFileHandler->remove(self::$user1ApiToken->token, $fileSource->getId(), $filename);

        self::expectException(NonSuccessResponseException::class);
        self::expectExceptionCode(404);
        self::$client->fileSourceFileHandler->read(self::$user1ApiToken->token, $fileSource->getId(), $filename);
    }
}
