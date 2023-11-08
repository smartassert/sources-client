<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\GitSourceClient;

use SmartAssert\SourcesClient\Exception\InvalidRequestException;
use SmartAssert\SourcesClient\Exception\ModifyReadOnlyEntityException;
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\InvalidRequestField;
use SmartAssert\SourcesClient\Tests\DataProvider\CreateUpdateGitSourceDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class UpdateTest extends AbstractIntegrationTestCase
{
    use CreateUpdateGitSourceDataProviderTrait;

    private GitSource $gitSource;

    protected function setUp(): void
    {
        parent::setUp();

        $gitSource = self::$gitSourceClient->create(
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
    public function testUpdateInvalidRequest(
        string $label,
        string $hostUrl,
        string $path,
        InvalidRequestField $expected
    ): void {
        try {
            self::$gitSourceClient->update(
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
    public function testUpdateSuccess(string $label, string $hostUrl, string $path, ?string $credentials): void
    {
        $gitSource = self::$gitSourceClient->update(
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

    public function testUpdateDeletedFileSource(): void
    {
        self::$gitSourceClient->delete(self::$user1ApiToken->token, $this->gitSource->getId());

        self::expectException(ModifyReadOnlyEntityException::class);
        self::expectExceptionMessage('Cannot modify read-only source ' . $this->gitSource->getId() . '.');

        self::$gitSourceClient->update(
            self::$user1ApiToken->token,
            $this->gitSource->getId(),
            md5((string) rand()),
            'https://example.com/' . md5((string) rand()) . '.git',
            '/' . md5((string) rand()),
            null
        );
    }
}
