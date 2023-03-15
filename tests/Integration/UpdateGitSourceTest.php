<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use SmartAssert\SourcesClient\Exception\InvalidRequestException;
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\InvalidRequestField;
use SmartAssert\SourcesClient\Tests\DataProvider\CreateUpdateGitSourceDataProviderTrait;

class UpdateGitSourceTest extends AbstractIntegrationTestCase
{
    use CreateUpdateGitSourceDataProviderTrait;

    private GitSource $gitSource;

    protected function setUp(): void
    {
        parent::setUp();

        $gitSource = self::$client->createGitSource(
            self::$user1ApiToken->token,
            md5((string) rand()),
            'https://example.com/' . md5((string) rand()) . '.git',
            md5((string) rand()),
            null
        );
        \assert($gitSource instanceof GitSource);
        $this->gitSource = $gitSource;
    }

    /**
     * @dataProvider createUpdateGitSourceInvalidRequestDataProvider
     *
     * @param non-empty-string $label
     * @param non-empty-string $hostUrl
     * @param non-empty-string $path
     */
    public function testUpdateGitSourceInvalidRequest(
        string $label,
        string $hostUrl,
        string $path,
        InvalidRequestField $expected
    ): void {
        try {
            self::$client->updateGitSource(
                self::$user1ApiToken->token,
                $this->gitSource->getId(),
                $label,
                $hostUrl,
                $path,
                null
            );
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidRequestException::class, $e);
            self::assertEquals($expected, $e->getInvalidRequestField());
        }
    }

    /**
     * @dataProvider createUpdateGitSourceSuccessDataProvider
     *
     * @param non-empty-string  $label
     * @param non-empty-string  $hostUrl
     * @param non-empty-string  $path
     * @param ?non-empty-string $credentials
     */
    public function testUpdateGitSourceSuccess(string $label, string $hostUrl, string $path, ?string $credentials): void
    {
        $gitSource = self::$client->updateGitSource(
            self::$user1ApiToken->token,
            $this->gitSource->getId(),
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
}
