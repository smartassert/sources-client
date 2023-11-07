<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;

interface FileClientInterface
{
    /**
     * @param non-empty-string $token
     * @param non-empty-string $fileSourceId
     * @param non-empty-string $filename
     *
     * @throws HttpResponseExceptionInterface
     * @throws ClientExceptionInterface
     * @throws UnauthorizedException
     */
    public function add(string $token, string $fileSourceId, string $filename, string $content): void;

    /**
     * @param non-empty-string $token
     * @param non-empty-string $fileSourceId
     * @param non-empty-string $filename
     *
     * @throws ClientExceptionInterface
     * @throws NonSuccessResponseException
     * @throws HttpResponseExceptionInterface
     * @throws UnauthorizedException
     */
    public function read(string $token, string $fileSourceId, string $filename): string;

    /**
     * @param non-empty-string $token
     * @param non-empty-string $fileSourceId
     * @param non-empty-string $filename
     *
     * @throws ClientExceptionInterface
     * @throws NonSuccessResponseException
     * @throws HttpResponseExceptionInterface
     * @throws UnauthorizedException
     */
    public function remove(string $token, string $fileSourceId, string $filename): void;
}
