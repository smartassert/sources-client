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
     * @param non-empty-string      $label
     * @param InvalidRequestField[] $expectedInvalidRequestFields
     */
    public function testCreateFileSourceInvalidRequest(string $label, array $expectedInvalidRequestFields): void
    {
        $invalidRequestError = self::$client->createFileSource(self::$user1ApiToken->token, $label);

        self::assertInstanceOf(InvalidRequestError::class, $invalidRequestError);
        self::assertEquals($expectedInvalidRequestFields, $invalidRequestError->getInvalidRequestFields());
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
                'expectedInvalidRequestFields' => [
                    'label' => new InvalidRequestField(
                        'label',
                        '',
                        'This value is too short. It should have 1 character or more.'
                    ),
                ],
            ],
            'label too long' => [
                'label' => $labelTooLong,
                'expectedInvalidRequestFields' => [
                    'label' => new InvalidRequestField(
                        'label',
                        $labelTooLong,
                        'This value is too long. It should have 255 characters or less.',
                    ),
                ],
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
