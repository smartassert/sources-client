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
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\SourcesClient\Model\SerializedSuite;

class SerializedSuiteClient implements SerializedSuiteClientInterface
{
    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SerializedSuiteFactory $serializedSuiteFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

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

    public function get(string $token, string $serializedSuiteId): SerializedSuite
    {
        return $this->handleSerializedSuiteRetrievalRequest(
            $this->requestFactory->createSerializedSuiteRequest($token, $serializedSuiteId)
        );
    }

    public function read(string $token, string $serializedSuiteId): string
    {
        try {
            $response = $this->serviceClient->sendRequest(
                $this->requestFactory->createReadSerializedSuiteRequest($token, $serializedSuiteId)
            );
        } catch (NonSuccessResponseException $e) {
            throw $this->exceptionFactory->createFromResponse($e->getResponse());
        }

        return $response->getHttpResponse()->getBody()->getContents();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     * @throws UnauthorizedException
     */
    private function handleSerializedSuiteRetrievalRequest(Request $request): SerializedSuite
    {
        try {
            $response = $this->serviceClient->sendRequest($request);
        } catch (NonSuccessResponseException $e) {
            throw $this->exceptionFactory->createFromResponse($e->getResponse());
        }

        if (!$response instanceof JsonResponse) {
            throw InvalidResponseTypeException::create($response, JsonResponse::class);
        }

        $serializedSuite = $this->serializedSuiteFactory->create($response->getData());
        if (null === $serializedSuite) {
            throw InvalidModelDataException::fromJsonResponse(SerializedSuite::class, $response);
        }

        return $serializedSuite;
    }
}
