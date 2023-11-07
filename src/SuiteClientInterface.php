<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\SourcesClient\Exception\ModifyReadOnlyEntityException;
use SmartAssert\SourcesClient\Model\Suite;

interface SuiteClientInterface
{
    /**
     * @param non-empty-string   $token
     * @param non-empty-string   $sourceId
     * @param non-empty-string   $label
     * @param non-empty-string[] $tests
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws UnauthorizedException
     */
    public function create(string $token, string $sourceId, string $label, array $tests): Suite;

    /**
     * @param non-empty-string   $token
     * @param non-empty-string   $suiteId
     * @param non-empty-string   $sourceId
     * @param non-empty-string   $label
     * @param non-empty-string[] $tests
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws ModifyReadOnlyEntityException
     * @throws UnauthorizedException
     */
    public function update(string $token, string $suiteId, string $sourceId, string $label, array $tests): Suite;

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws UnauthorizedException
     */
    public function delete(string $token, string $suiteId): Suite;

    /**
     * @return Suite[]
     *
     * @throws ClientExceptionInterface
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     * @throws InvalidResponseTypeException
     * @throws HttpResponseExceptionInterface
     * @throws UnauthorizedException
     */
    public function list(string $token): array;
}
