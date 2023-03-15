<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\RequestHandler;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\SourcesClient\Model\SourceInterface;

class SourceMutationHandler extends AbstractSourceHandler
{
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
