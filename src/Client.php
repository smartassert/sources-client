<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\SourcesClient\Model\ErrorInterface;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\SourceInterface;

class Client
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly ServiceClient $serviceClient,
        private readonly ErrorFactory $errorFactory,
        private readonly SourceFactory $sourceFactory,
    ) {
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $label
     *
     * @throws ClientExceptionInterface
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     * @throws InvalidModelDataException
     */
    public function createFileSource(string $token, string $label): FileSource|ErrorInterface
    {
        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            (new Request('POST', $this->createUrl('/source')))
                ->withAuthentication(new BearerAuthentication($token))
                ->withPayload(new UrlEncodedPayload([
                    'type' => 'file',
                    'label' => $label,
                ]))
        );

        if (400 === $response->getStatusCode()) {
            return $this->createErrorModel($response);
        }

        if (!$response->isSuccessful()) {
            throw new NonSuccessResponseException($response->getHttpResponse());
        }

        $source = $this->sourceFactory->createFileSource($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(FileSource::class, $response);
        }

        return $source;
    }

    /**
     * @param non-empty-string  $label
     * @param non-empty-string  $token
     * @param non-empty-string  $hostUrl
     * @param non-empty-string  $path
     * @param ?non-empty-string $credentials
     *
     * @throws ClientExceptionInterface
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     * @throws InvalidModelDataException
     */
    public function createGitSource(
        string $token,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
    ): GitSource|ErrorInterface {
        $payload = [
            'type' => 'git',
            'label' => $label,
            'host-url' => $hostUrl,
            'path' => $path,
        ];

        if (is_string($credentials)) {
            $payload['credentials'] = $credentials;
        }

        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            (new Request('POST', $this->createUrl('/source')))
                ->withAuthentication(new BearerAuthentication($token))
                ->withPayload(new UrlEncodedPayload($payload))
        );

        if (400 === $response->getStatusCode()) {
            return $this->createErrorModel($response);
        }

        if (!$response->isSuccessful()) {
            throw new NonSuccessResponseException($response->getHttpResponse());
        }

        $source = $this->sourceFactory->createGitSource($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(GitSource::class, $response);
        }

        return $source;
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $fileSourceId
     * @param non-empty-string $filename
     *
     * @throws ClientExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     */
    public function addFile(string $token, string $fileSourceId, string $filename, string $content): ?ErrorInterface
    {
        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            (new Request('POST', $this->createUrl('/source/' . urlencode($fileSourceId) . '/' . urlencode($filename))))
                ->withAuthentication(new BearerAuthentication($token))
                ->withPayload(new Payload('text/x-yaml', $content))
        );

        if (400 === $response->getStatusCode()) {
            return $this->createErrorModel($response);
        }

        if (!$response->isSuccessful()) {
            throw new NonSuccessResponseException($response->getHttpResponse());
        }

        return null;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws NonSuccessResponseException
     */
    public function readFile(string $token, string $fileSourceId, string $filename): string
    {
        $response = $this->serviceClient->sendRequest(
            (new Request('GET', $this->createUrl('/source/' . urlencode($fileSourceId) . '/' . urlencode($filename))))
                ->withAuthentication(new BearerAuthentication($token))
        );

        if (!$response->isSuccessful()) {
            throw new NonSuccessResponseException($response->getHttpResponse());
        }

        return $response->getHttpResponse()->getBody()->getContents();
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
        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            (new Request('GET', $this->createUrl('/sources')))
                ->withAuthentication(new BearerAuthentication($token))
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
     * @throws NonSuccessResponseException
     */
    public function removeFile(string $token, string $fileSourceId, string $filename): void
    {
        $response = $this->serviceClient->sendRequest(
            (new Request(
                'DELETE',
                $this->createUrl('/source/' . urlencode($fileSourceId) . '/' . urlencode($filename))
            )
            )
                ->withAuthentication(new BearerAuthentication($token))
        );

        if (!$response->isSuccessful()) {
            throw new NonSuccessResponseException($response->getHttpResponse());
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     */
    public function getSource(string $token, string $sourceId): SourceInterface
    {
        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            (new Request('GET', $this->createUrl('/source/' . urlencode($sourceId))))
                ->withAuthentication(new BearerAuthentication($token))
        );

        if (!$response->isSuccessful()) {
            throw new NonSuccessResponseException($response->getHttpResponse());
        }

        $source = $this->sourceFactory->create($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(SourceInterface::class, $response);
        }

        return $source;
    }

    /**
     * @param non-empty-string $path
     *
     * @return non-empty-string
     */
    private function createUrl(string $path): string
    {
        return rtrim($this->baseUrl, '/') . $path;
    }

    /**
     * @throws InvalidModelDataException
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     */
    private function createErrorModel(JsonResponse $response): ErrorInterface
    {
        $error = $this->errorFactory->create(
            $response->getHttpResponse(),
            new ArrayInspector($response->getData())
        );

        if (null === $error) {
            throw InvalidModelDataException::fromJsonResponse(ErrorInterface::class, $response);
        }

        return $error;
    }
}
