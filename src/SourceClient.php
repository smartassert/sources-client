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
use SmartAssert\SourcesClient\Exception\ModifyReadOnlyEntityException;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Request\FileSourceRequest;
use SmartAssert\SourcesClient\Request\GitSourceRequest;
use SmartAssert\SourcesClient\Request\RequestInterface;
use SmartAssert\SourcesClient\Request\SourceRequest;

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
     * @param non-empty-string $sourceId
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     */
    public function get(string $token, string $sourceId): SourceInterface
    {
        return $this->handleSourceRequest(new SourceRequest('GET', $sourceId), $token);
    }

    /**
     * @param non-empty-string $sourceId
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     */
    public function delete(string $token, string $sourceId): SourceInterface
    {
        return $this->handleSourceRequest(new SourceRequest('DELETE', $sourceId), $token);
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
        return $this->handleSourceRequest(new FileSourceRequest('POST', $label), $token);
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $sourceId
     * @param non-empty-string $label
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws ModifyReadOnlyEntityException
     */
    public function updateFileSource(string $token, string $sourceId, string $label): SourceInterface
    {
        try {
            return $this->handleSourceRequest(new FileSourceRequest('PUT', $label, $sourceId), $token);
        } catch (NonSuccessResponseException $e) {
            if (405 === $e->getCode()) {
                throw new ModifyReadOnlyEntityException($sourceId, 'source');
            }

            throw $e;
        }
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
        return $this->handleSourceRequest(
            new GitSourceRequest('POST', $label, $hostUrl, $path, $credentials),
            $token
        );
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
     * @throws ModifyReadOnlyEntityException
     */
    public function updateGitSource(
        string $token,
        string $sourceId,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
    ): SourceInterface {
        try {
            return $this->handleSourceRequest(
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
     */
    private function handleSourceRequest(RequestInterface $request, string $token): SourceInterface
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory
                ->createSourceRequest($request, $token)
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
