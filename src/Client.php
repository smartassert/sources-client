<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
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
