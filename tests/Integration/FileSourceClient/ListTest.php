<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\FileSourceClient;

use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class ListTest extends AbstractIntegrationTestCase
{
    public function testListSuccess(): void
    {
        $fileSource = self::$fileSourceClient->create(self::$user1ApiToken->token, md5((string) rand()));

        self::assertSame(
            [],
            self::$fileSourceClient->list(self::$user1ApiToken->token, $fileSource->getId())
        );

        $filenames = [];
        $fileCount = rand(1, 10);
        for ($fileIndex = 0; $fileIndex < $fileCount; ++$fileIndex) {
            $filename = md5((string) rand()) . '.yaml';
            $content = md5((string) rand());

            $filenames[] = $filename;

            self::$fileClient->add(
                self::$user1ApiToken->token,
                $fileSource->getId(),
                $filename,
                $content
            );
        }

        sort($filenames);

        self::assertSame(
            $filenames,
            self::$fileSourceClient->list(self::$user1ApiToken->token, $fileSource->getId())
        );
    }
}
