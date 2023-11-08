<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\SourcesClient\Exception\ModifyReadOnlyEntityException;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Request\FileSourceRequest;
use SmartAssert\SourcesClient\Request\RequestInterface;
use SmartAssert\SourcesClient\Request\SourceRequest;

class FileSourceClient implements FileSourceClientInterface
{
    use VerifyJsonResponseTrait;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SourceFactory $sourceFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    public function get(string $token, string $sourceId): FileSource
    {
        return $this->handleRequest(new SourceRequest('GET', $sourceId), $token);
    }

    public function list(string $token, string $fileSourceId): array
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

    public function create(string $token, string $label): FileSource
    {
        return $this->handleRequest(new FileSourceRequest('POST', $label), $token);
    }

    public function update(string $token, string $sourceId, string $label): FileSource
    {
        try {
            return $this->handleRequest(new FileSourceRequest('PUT', $label, $sourceId), $token);
        } catch (NonSuccessResponseException $e) {
            if (405 === $e->getCode()) {
                throw new ModifyReadOnlyEntityException($sourceId, 'source');
            }

            throw $e;
        }
    }

    public function delete(string $token, string $sourceId): FileSource
    {
        return $this->handleRequest(new SourceRequest('DELETE', $sourceId), $token);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws UnauthorizedException
     */
    private function handleRequest(RequestInterface $request, string $token): FileSource
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory
                ->createSourceRequest($request, $token)
                ->withPayload(new UrlEncodedPayload($request->getPayload()))
        );

        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

        $source = $this->sourceFactory->createFileSource($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(SourceInterface::class, $response);
        }

        return $source;
    }
}