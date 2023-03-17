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
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\ServiceClient\Response\ResponseInterface;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Request\FileSourceRequest;
use SmartAssert\SourcesClient\Request\GitSourceRequest;
use SmartAssert\SourcesClient\Request\SourceRequestInterface;

class SourceClient
{
    use VerifyJsonResponseTrait;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SourceFactory $sourceFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @return SourceInterface[]
     *
     * @throws ClientExceptionInterface
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     * @throws InvalidResponseTypeException
     * @throws HttpResponseExceptionInterface
     */
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

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     */
    public function get(string $token, string $sourceId): SourceInterface
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSourceRequest('GET', $token, $sourceId)
        );

        return $this->handleSourceResponse($response);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     */
    public function delete(string $token, string $sourceId): SourceInterface
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSourceRequest('DELETE', $token, $sourceId)
        );

        return $this->handleSourceResponse($response);
    }

    /**
     * @return string[]
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     */
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
        return $this->makeSourceMutationRequest($token, new FileSourceRequest($label));
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
        return $this->makeSourceMutationRequest($token, new FileSourceRequest($label, $sourceId));
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
        return $this->makeSourceMutationRequest($token, new GitSourceRequest($label, $hostUrl, $path, $credentials));
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
        return $this->makeSourceMutationRequest(
            $token,
            new GitSourceRequest($label, $hostUrl, $path, $credentials, $sourceId)
        );
    }

    /**
     * @param non-empty-string $token
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     */
    private function makeSourceMutationRequest(string $token, SourceRequestInterface $request): SourceInterface
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSourceRequest($request->hasId() ? 'PUT' : 'POST', $token, $request->getId())
                ->withPayload(new UrlEncodedPayload($request->getPayload()))
        );

        return $this->handleSourceResponse($response);
    }

    /**
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     * @throws HttpResponseExceptionInterface
     */
    private function handleSourceResponse(ResponseInterface $response): SourceInterface
    {
        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

        $source = $this->sourceFactory->create($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(SourceInterface::class, $response);
        }

        return $source;
    }
}
