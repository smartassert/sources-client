<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\SourcesClient\Model\SourceInterface;

class SourceClient extends AbstractSourceClient
{
    /**
     * @return SourceInterface[]
     *
     * @throws ClientExceptionInterface
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     */
    public function list(string $token): array
    {
        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            $this->requestFactory->createSourcesRequest($token)
        );

        if (!$response->isSuccessful()) {
            throw new NonSuccessResponseException($response->getHttpResponse());
        }

        $sources = [];

        foreach ($response->getData() as $sourceData) {
            if (is_array($sourceData)) {
                $source = $this->sourceFactory->create($sourceData);

                if ($source instanceof SourceInterface) {
                    $sources[] = $source;
                }
            }
        }

        return $sources;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function get(string $token, string $sourceId): SourceInterface
    {
        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            $this->requestFactory->createSourceRequest('GET', $token, $sourceId)
        );

        if (!$response->isSuccessful()) {
            throw $this->exceptionFactory->createFromResponse($response);
        }

        $source = $this->sourceFactory->create($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(SourceInterface::class, $response);
        }

        return $source;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    public function delete(string $token, string $sourceId): SourceInterface
    {
        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            $this->requestFactory->createSourceRequest('DELETE', $token, $sourceId)
        );

        if (!$response->isSuccessful()) {
            throw $this->exceptionFactory->createFromResponse($response);
        }

        $source = $this->sourceFactory->create($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(SourceInterface::class, $response);
        }

        return $source;
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
    public function listFiles(string $token, string $fileSourceId): array
    {
        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            $this->requestFactory->createSourceFilenamesRequest($token, $fileSourceId)
        );

        if (!$response->isSuccessful()) {
            throw $this->exceptionFactory->createFromResponse($response);
        }

        $filenames = [];
        foreach ($response->getData() as $item) {
            if (is_string($item)) {
                $filenames[] = $item;
            }
        }

        return $filenames;
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
        return $this->makeFileSourceMutationRequest($token, $label, null);
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
        return $this->makeFileSourceMutationRequest($token, $label, $sourceId);
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
        return $this->makeGitSourceMutationRequest($token, $label, $hostUrl, $path, $credentials, null);
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
        return $this->makeGitSourceMutationRequest($token, $label, $hostUrl, $path, $credentials, $sourceId);
    }

    /**
     * @param non-empty-string      $token
     * @param non-empty-string      $label
     * @param null|non-empty-string $sourceId
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    private function makeFileSourceMutationRequest(
        string $token,
        string $label,
        ?string $sourceId,
    ): SourceInterface {
        return $this->makeSourceMutationRequest($token, ['type' => 'file', 'label' => $label], $sourceId);
    }

    /**
     * @param non-empty-string      $label
     * @param non-empty-string      $token
     * @param non-empty-string      $hostUrl
     * @param non-empty-string      $path
     * @param null|non-empty-string $credentials
     * @param null|non-empty-string $sourceId
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    private function makeGitSourceMutationRequest(
        string $token,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
        ?string $sourceId,
    ): SourceInterface {
        $payload = [
            'type' => 'git',
            'label' => $label,
            'host-url' => $hostUrl,
            'path' => $path,
        ];

        if (is_string($credentials)) {
            $payload['credentials'] = $credentials;
        }

        return $this->makeSourceMutationRequest($token, $payload, $sourceId);
    }

    /**
     * @param non-empty-string      $token
     * @param null|non-empty-string $sourceId
     * @param array<mixed>          $payload
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    private function makeSourceMutationRequest(
        string $token,
        array $payload,
        ?string $sourceId,
    ): SourceInterface {
        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            $this->requestFactory->createSourceRequest(is_string($sourceId) ? 'PUT' : 'POST', $token, $sourceId)
                ->withPayload(new UrlEncodedPayload($payload))
        );

        if (!$response->isSuccessful()) {
            throw $this->exceptionFactory->createFromResponse($response);
        }

        $source = $this->sourceFactory->create($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(SourceInterface::class, $response);
        }

        return $source;
    }
}
