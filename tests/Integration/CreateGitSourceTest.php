<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\InvalidRequestError;
use SmartAssert\SourcesClient\Model\InvalidRequestField;

class CreateGitSourceTest extends AbstractIntegrationTestCase
{
    /**
     * @dataProvider createGitSourceInvalidRequestDataProvider
     *
     * @param non-empty-string      $label
     * @param non-empty-string      $hostUrl
     * @param non-empty-string      $path
     * @param InvalidRequestField[] $expectedInvalidRequestFields
     */
    public function testCreateGitSourceInvalidRequest(
        string $label,
        string $hostUrl,
        string $path,
        array $expectedInvalidRequestFields
    ): void {
        $invalidRequestError = self::$client->createGitSource(
            self::$user1ApiToken->token,
            $label,
            $hostUrl,
            $path,
            null
        );

        self::assertInstanceOf(InvalidRequestError::class, $invalidRequestError);
        self::assertEquals($expectedInvalidRequestFields, $invalidRequestError->invalidRequestFields);
    }

    /**
     * @return array<mixed>
     */
    public function createGitSourceInvalidRequestDataProvider(): array
    {
        $label = 'git source label';
        $labelTooLong = str_repeat('.', 256);
        $hostUrl = 'https://example.com/repository.git';
        $hostUrlTooLong = str_repeat('.', 256);
        $path = '/';
        $pathUrlTooLong = str_repeat('.', 256);

        return [
            'label missing' => [
                'label' => '  ',
                'hostUrl' => $hostUrl,
                'path' => $path,
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
                'hostUrl' => $hostUrl,
                'path' => $path,
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

    public function testCreateGitSourceSuccess(): void
    {
        $label = md5((string) rand());

        $fileSource = self::$client->createFileSource(self::$user1ApiToken->token, $label);

        self::assertInstanceOf(FileSource::class, $fileSource);
        self::assertSame($label, $fileSource->label);
        self::assertNotEmpty($fileSource->id);
    }
}
