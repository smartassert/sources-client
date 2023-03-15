<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\RequestHandler\FileHandler;
use SmartAssert\SourcesClient\RequestHandler\SourceHandler;

class Client
{
    public function __construct(
        public readonly FileHandler $fileSourceFileHandler,
        public readonly SourceHandler $sourceHandler,
    ) {
    }

    /**
     * @return SourceInterface[]
     *
     * @throws ClientExceptionInterface
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     */
    public function listSources(string $token): array
    {
        return $this->sourceHandler->list($token);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function getSource(string $token, string $sourceId): SourceInterface
    {
        return $this->sourceHandler->get($token, $sourceId);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function deleteSource(string $token, string $sourceId): SourceInterface
    {
        return $this->sourceHandler->delete($token, $sourceId);
    }

    /**
     * @return string[]
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function listFileSourceFilenames(string $token, string $fileSourceId): array
    {
        return $this->sourceHandler->listFiles($token, $fileSourceId);
    }
}
