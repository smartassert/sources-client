<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\InvalidRequestError;
use SmartAssert\SourcesClient\Model\InvalidRequestField;

class CreateFileSourceTest extends AbstractIntegrationTestCase
{
    /**
     * @dataProvider createFileSourceInvalidRequestDataProvider
     *
     * @param non-empty-string $label
     */
    public function testCreateFileSourceInvalidRequest(string $label, InvalidRequestField $expected): void
    {
        $invalidRequestError = self::$client->createFileSource(self::$user1ApiToken->token, $label);

        self::assertInstanceOf(InvalidRequestError::class, $invalidRequestError);
        self::assertEquals($expected, $invalidRequestError->getInvalidRequestField());
    }

    /**
     * @return array<mixed>
     */
    public function createFileSourceInvalidRequestDataProvider(): array
    {
        $labelTooLong = str_repeat('.', 256);

        return [
            'label missing' => [
                'label' => '  ',
                'expected' => new InvalidRequestField(
                    'label',
                    '',
                    'This value should be between 1 and 255 characters long.'
                ),
            ],
            'label too long' => [
                'label' => $labelTooLong,
                'expected' => new InvalidRequestField(
                    'label',
                    $labelTooLong,
                    'This value should be between 1 and 255 characters long.',
                ),
            ],
        ];
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
