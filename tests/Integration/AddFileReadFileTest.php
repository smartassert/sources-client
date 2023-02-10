<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Model\FileSource;

class AddFileReadFileTest extends AbstractIntegrationTestCase
{
    public function testAddFileReadFile(): void
    {
        $label = md5((string) rand());

        $fileSource = self::$client->createFileSource(self::$user1ApiToken->token, $label);
        self::assertInstanceOf(FileSource::class, $fileSource);

        $filename = md5((string) rand()) . '.yaml';
        $content = md5((string) rand());

        $addFileResponse = self::$client->addFile(self::$user1ApiToken->token, $fileSource->id, $filename, $content);
        self::assertNull($addFileResponse);

        $readFileResponse = self::$client->readFile(self::$user1ApiToken->token, $fileSource->id, $filename);
        self::assertSame($content, $readFileResponse);
    }
}
