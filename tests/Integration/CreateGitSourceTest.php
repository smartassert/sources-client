<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\InvalidRequestError;
use SmartAssert\SourcesClient\Model\InvalidRequestField;
use SmartAssert\SourcesClient\Tests\DataProvider\CreateUpdateGitSourceDataProviderTrait;

class CreateGitSourceTest extends AbstractIntegrationTestCase
{
    use CreateUpdateGitSourceDataProviderTrait;

    /**
     * @dataProvider createUpdateGitSourceInvalidRequestDataProvider
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
