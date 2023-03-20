<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
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
     * @param non-empty-string                          $suiteId
     * @param array<non-empty-string, non-empty-string> $parameters
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    public function create(string $token, string $suiteId, array $parameters = []): SerializedSuite
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSuiteSerializationRequest($token, $suiteId)
                ->withPayload(new UrlEncodedPayload($parameters))
        );

        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

        $serializedSuite = $this->serializedSuiteFactory->create($response->getData());
        if (null === $serializedSuite) {
            throw InvalidModelDataException::fromJsonResponse(SerializedSuite::class, $response);
        }

        return $serializedSuite;
    }
}
