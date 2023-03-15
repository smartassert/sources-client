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
use SmartAssert\SourcesClient\RequestHandler\SourceAccessHandler;
use SmartAssert\SourcesClient\RequestHandler\SourceMutationHandler;

class Client
{
    public function __construct(
        public readonly FileHandler $fileSourceFileHandler,
        private readonly SourceMutationHandler $sourceMutationHandler,
        private readonly SourceAccessHandler $sourceAccessHandler,
    ) {
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $label
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    public function createFileSource(string $token, string $label): SourceInterface
    {
        return $this->sourceMutationHandler->createFileSource($token, $label);
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $sourceId
     * @param non-empty-string $label
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    public function updateFileSource(string $token, string $sourceId, string $label): SourceInterface
    {
        return $this->sourceMutationHandler->updateFileSource($token, $sourceId, $label);
    }

    /**
     * @param non-empty-string  $label
     * @param non-empty-string  $token
     * @param non-empty-string  $hostUrl
     * @param non-empty-string  $path
     * @param ?non-empty-string $credentials
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    public function createGitSource(
        string $token,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
    ): SourceInterface {
        return $this->sourceMutationHandler->createGitSource($token, $label, $hostUrl, $path, $credentials);
    }

    /**
     * @param non-empty-string  $label
     * @param non-empty-string  $token
     * @param non-empty-string  $sourceId
     * @param non-empty-string  $hostUrl
     * @param non-empty-string  $path
     * @param ?non-empty-string $credentials
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    public function updateGitSource(
        string $token,
        string $sourceId,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
    ): SourceInterface {
        return $this->sourceMutationHandler->updateGitSource($token, $sourceId, $label, $hostUrl, $path, $credentials);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws NonSuccessResponseException
     * @throws HttpResponseExceptionInterface
     */
    public function readFile(string $token, string $fileSourceId, string $filename): string
    {
        return $this->fileSourceFileHandler->read($token, $fileSourceId, $filename);
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
        return $this->sourceAccessHandler->list($token);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws NonSuccessResponseException
     * @throws HttpResponseExceptionInterface
     */
    public function removeFile(string $token, string $fileSourceId, string $filename): void
    {
        $this->fileSourceFileHandler->remove($token, $fileSourceId, $filename);
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
        return $this->sourceAccessHandler->get($token, $sourceId);
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
        return $this->sourceAccessHandler->delete($token, $sourceId);
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
        return $this->sourceAccessHandler->listFiles($token, $fileSourceId);
    }
}
