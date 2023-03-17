<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\SourcesClient\Model\Suite;
use SmartAssert\SourcesClient\Request\RequestInterface;
use SmartAssert\SourcesClient\Request\SuiteRequest;

class SuiteClient
{
    use VerifyJsonResponseTrait;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SuiteFactory $suiteFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @param non-empty-string   $token
     * @param non-empty-string   $sourceId
     * @param non-empty-string   $label
     * @param non-empty-string[] $tests
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    public function create(string $token, string $sourceId, string $label, array $tests): Suite
    {
        return $this->makeMutationRequest($token, new SuiteRequest($sourceId, $label, $tests));
    }

    /**
     * @param non-empty-string   $token
     * @param non-empty-string   $suiteId
     * @param non-empty-string   $sourceId
     * @param non-empty-string   $label
     * @param non-empty-string[] $tests
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    public function update(string $token, string $suiteId, string $sourceId, string $label, array $tests): Suite
    {
        return $this->makeMutationRequest($token, new SuiteRequest($sourceId, $label, $tests, $suiteId));
    }

    /**
     * @param non-empty-string $token
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    private function makeMutationRequest(string $token, RequestInterface $request): Suite
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSuiteRequest($request->hasId() ? 'PUT' : 'POST', $token, $request->getId())
                ->withPayload(new UrlEncodedPayload($request->getPayload()))
        );

        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

        $suite = $this->suiteFactory->create($response->getData());
        if (null === $suite) {
            throw InvalidModelDataException::fromJsonResponse(Suite::class, $response);
        }

        return $suite;
    }
}
