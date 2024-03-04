<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\FileClient;

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

    public function testAddReadUpdateDeleteFile(): void
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

        self::$fileClient->remove(self::$user1ApiToken->token, $fileSource->getId(), $filename);
    }
}
