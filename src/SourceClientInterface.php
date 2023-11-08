<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Exception\CurlExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\SourcesClient\Exception\ModifyReadOnlyEntityException;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\SourceInterface;

interface SourceClientInterface
{
    /**
     * @return SourceInterface[]
     *
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     * @throws UnauthorizedException
     */
    public function list(string $token): array;

    /**
     * @param non-empty-string $sourceId
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws UnauthorizedException
     */
    public function get(string $token, string $sourceId): SourceInterface;

    /**
     * @param non-empty-string $sourceId
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws UnauthorizedException
     */
    public function delete(string $token, string $sourceId): SourceInterface;

    /**
     * @return string[]
     *
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     * @throws UnauthorizedException
     */
    public function listFiles(string $token, string $fileSourceId): array;

    /**
     * @param non-empty-string $token
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws UnauthorizedException
     */
    public function createFileSource(string $token, string $label): FileSource;

    /**
     * @param non-empty-string $token
     * @param non-empty-string $sourceId
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws ModifyReadOnlyEntityException
     * @throws UnauthorizedException
     */
    public function updateFileSource(string $token, string $sourceId, string $label): FileSource;

    /**
     * @param non-empty-string  $token
     * @param non-empty-string  $hostUrl
     * @param non-empty-string  $path
     * @param ?non-empty-string $credentials
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws UnauthorizedException
     */
    public function createGitSource(
        string $token,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
    ): GitSource;

    /**
     * @param non-empty-string  $token
     * @param non-empty-string  $sourceId
     * @param non-empty-string  $hostUrl
     * @param non-empty-string  $path
     * @param ?non-empty-string $credentials
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws ModifyReadOnlyEntityException
     * @throws UnauthorizedException
     */
    public function updateGitSource(
        string $token,
        string $sourceId,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
    ): GitSource;
}
