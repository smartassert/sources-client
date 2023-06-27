<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\ServiceClient\Request;
use SmartAssert\SourcesClient\Model\SerializedSuite;

class SerializedSuiteClient
{
    use VerifyJsonResponseTrait;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SerializedSuiteFactory $serializedSuiteFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @param non-empty-string                          $token
     * @param non-empty-string                          $serializedSuiteId
     * @param non-empty-string                          $suiteId
     * @param array<non-empty-string, non-empty-string> $parameters
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    public function create(
        string $token,
        string $serializedSuiteId,
        string $suiteId,
        array $parameters = []
    ): SerializedSuite {
        return $this->handleSerializedSuiteRetrievalRequest(
            $this->requestFactory->createSuiteSerializationRequest($token, $serializedSuiteId, $suiteId)
                ->withPayload(new UrlEncodedPayload($parameters))
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     */
    public function get(string $token, string $serializedSuiteId): SerializedSuite
    {
        return $this->handleSerializedSuiteRetrievalRequest(
            $this->requestFactory->createSerializedSuiteRequest($token, $serializedSuiteId)
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidResponseDataException
     */
    public function read(string $token, string $serializedSuiteId): string
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createReadSerializedSuiteRequest($token, $serializedSuiteId)
        );

        if (!$response->isSuccessful()) {
            throw $this->exceptionFactory->createFromResponse($response);
        }

        return $response->getHttpResponse()->getBody()->getContents();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     */
    private function handleSerializedSuiteRetrievalRequest(Request $request): SerializedSuite
    {
        $response = $this->verifyJsonResponse(
            $this->serviceClient->sendRequest($request),
            $this->exceptionFactory
        );

        $serializedSuite = $this->serializedSuiteFactory->create($response->getData());
        if (null === $serializedSuite) {
            throw InvalidModelDataException::fromJsonResponse(SerializedSuite::class, $response);
        }

        return $serializedSuite;
    }
}
