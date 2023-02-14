<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\InvalidRequestError;
use SmartAssert\SourcesClient\Model\InvalidRequestField;

class CreateGitSourceTest extends AbstractIntegrationTestCase
{
    /**
     * @dataProvider createGitSourceInvalidRequestDataProvider
     *
     * @param non-empty-string $label
     * @param non-empty-string $hostUrl
     * @param non-empty-string $path
     */
    public function testCreateGitSourceInvalidRequest(
        string $label,
        string $hostUrl,
        string $path,
        InvalidRequestField $expected
    ): void {
        $invalidRequestError = self::$client->createGitSource(
            self::$user1ApiToken->token,
            $label,
            $hostUrl,
            $path,
            null
        );

        self::assertInstanceOf(InvalidRequestError::class, $invalidRequestError);
        self::assertEquals($expected, $invalidRequestError->getInvalidRequestField());
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
        $pathTooLong = str_repeat('.', 256);

        return [
            'label missing' => [
                'label' => '  ',
                'hostUrl' => $hostUrl,
                'path' => $path,
                'expected' => new InvalidRequestField(
                    'label',
                    '',
                    'This value is too short. It should have 1 character or more.'
                ),
            ],
            'label too long' => [
                'label' => $labelTooLong,
                'hostUrl' => $hostUrl,
                'path' => $path,
                'expected' => new InvalidRequestField(
                    'label',
                    $labelTooLong,
                    'This value is too long. It should have 255 characters or less.',
                ),
            ],
            'host url missing' => [
                'label' => $label,
                'hostUrl' => '   ',
                'path' => $path,
                'expected' => new InvalidRequestField(
                    'host-url',
                    '',
                    'This value is too short. It should have 1 character or more.'
                ),
            ],
            'host url too long' => [
                'label' => $label,
                'hostUrl' => $hostUrlTooLong,
                'path' => $path,
                'expected' => new InvalidRequestField(
                    'host-url',
                    $hostUrlTooLong,
                    'This value is too long. It should have 255 characters or less.',
                ),
            ],
            'path missing' => [
                'label' => $label,
                'hostUrl' => $hostUrl,
                'path' => '  ',
                'expected' => new InvalidRequestField(
                    'path',
                    '',
                    'This value is too short. It should have 1 character or more.'
                ),
            ],
            'path too long' => [
                'label' => $label,
                'hostUrl' => $hostUrl,
                'path' => $pathTooLong,
                'expected' => new InvalidRequestField(
                    'path',
                    $pathTooLong,
                    'This value is too long. It should have 255 characters or less.',
                ),
            ],
        ];
    }

    /**
     * @dataProvider createGitSourceSuccessDataProvider
     *
     * @param non-empty-string  $label
     * @param non-empty-string  $hostUrl
     * @param non-empty-string  $path
     * @param ?non-empty-string $credentials
     */
    public function testCreateGitSourceSuccess(string $label, string $hostUrl, string $path, ?string $credentials): void
    {
        $gitSource = self::$client->createGitSource(
            self::$user1ApiToken->token,
            $label,
            $hostUrl,
            $path,
            $credentials
        );

        self::assertInstanceOf(GitSource::class, $gitSource);
        self::assertSame($label, $gitSource->getLabel());
        self::assertSame($hostUrl, $gitSource->getHostUrl());
        self::assertSame($path, $gitSource->getPath());
        self::assertSame(is_string($credentials), $gitSource->hasCredentials());
        self::assertNotEmpty($gitSource->getId());
    }

    /**
     * @return array<mixed>
     */
    public function createGitSourceSuccessDataProvider(): array
    {
        return [
            'without credentials' => [
                'label' => md5((string) rand()),
                'hostUrl' => md5((string) rand()),
                'path' => md5((string) rand()),
                'credentials' => null,
            ],
            'with credentials' => [
                'label' => md5((string) rand()),
                'hostUrl' => md5((string) rand()),
                'path' => md5((string) rand()),
                'credentials' => md5((string) rand()),
            ],
        ];
    }
}
