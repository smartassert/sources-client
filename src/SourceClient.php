<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\ServiceClient\Response\ResponseInterface;
use SmartAssert\SourcesClient\Exception\ModifyReadOnlyEntityException;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Request\FileSourceRequest;
use SmartAssert\SourcesClient\Request\GitSourceRequest;
use SmartAssert\SourcesClient\Request\RequestInterface;
use SmartAssert\SourcesClient\Request\SourceRequest;

class SourceClient implements SourceClientInterface
{
    use VerifyJsonResponseTrait;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SourceFactory $sourceFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    public function list(string $token): array
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSourcesRequest($token)
        );

        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

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

    public function get(string $token, string $sourceId): SourceInterface
    {
        return $this->handleFileSourceRequest(new SourceRequest('GET', $sourceId), $token);
    }

    public function delete(string $token, string $sourceId): SourceInterface
    {
        return $this->handleFileSourceRequest(new SourceRequest('DELETE', $sourceId), $token);
    }

    public function listFiles(string $token, string $fileSourceId): array
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSourceFilenamesRequest($token, $fileSourceId)
        );

        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

        $filenames = [];
        foreach ($response->getData() as $item) {
            if (is_string($item)) {
                $filenames[] = $item;
            }
        }

        return $filenames;
    }

    public function createFileSource(string $token, string $label): FileSource
    {
        return $this->handleFileSourceRequest(new FileSourceRequest('POST', $label), $token);
    }

    public function updateFileSource(string $token, string $sourceId, string $label): FileSource
    {
        try {
            return $this->handleFileSourceRequest(new FileSourceRequest('PUT', $label, $sourceId), $token);
        } catch (NonSuccessResponseException $e) {
            if (405 === $e->getCode()) {
                throw new ModifyReadOnlyEntityException($sourceId, 'source');
            }

            throw $e;
        }
    }

    public function createGitSource(
        string $token,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
    ): GitSource {
        return $this->handleGitSourceRequest(
            new GitSourceRequest('POST', $label, $hostUrl, $path, $credentials),
            $token
        );
    }

    public function updateGitSource(
        string $token,
        string $sourceId,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
    ): GitSource {
        try {
            return $this->handleGitSourceRequest(
                new GitSourceRequest('PUT', $label, $hostUrl, $path, $credentials, $sourceId),
                $token
            );
        } catch (NonSuccessResponseException $e) {
            if (405 === $e->getCode()) {
                throw new ModifyReadOnlyEntityException($sourceId, 'source');
            }

            throw $e;
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws UnauthorizedException
     */
    private function handleFileSourceRequest(RequestInterface $request, string $token): FileSource
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory
                ->createSourceRequest($request, $token)
                ->withPayload(new UrlEncodedPayload($request->getPayload()))
        );

        return $this->handleFileSourceResponse($response);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws UnauthorizedException
     */
    private function handleGitSourceRequest(RequestInterface $request, string $token): GitSource
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory
                ->createSourceRequest($request, $token)
                ->withPayload(new UrlEncodedPayload($request->getPayload()))
        );

        return $this->handleGitSourceResponse($response);
    }

    /**
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     * @throws HttpResponseExceptionInterface
     */
    private function handleFileSourceResponse(ResponseInterface $response): FileSource
    {
        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

        $source = $this->sourceFactory->createFileSource($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(SourceInterface::class, $response);
        }

        return $source;
    }

    /**
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     * @throws HttpResponseExceptionInterface
     */
    private function handleGitSourceResponse(ResponseInterface $response): GitSource
    {
        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

        $source = $this->sourceFactory->createGitSource($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(SourceInterface::class, $response);
        }

        return $source;
    }
}
