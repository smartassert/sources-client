<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Exception\CurlExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;

interface FileClientInterface
{
    /**
     * @param non-empty-string $token
     *
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     * @throws UnauthorizedException
     */
    public function add(string $token, string $fileSourceId, string $filename, string $content): void;

    /**
     * @param non-empty-string $token
     *
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     * @throws UnauthorizedException
     */
    public function remove(string $token, string $fileSourceId, string $filename): void;
}
