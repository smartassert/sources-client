<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\RequestHandler;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\ExceptionFactory;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\RequestFactory;
use SmartAssert\SourcesClient\SourceFactory;

class SourceAccessHandler
{
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
}
