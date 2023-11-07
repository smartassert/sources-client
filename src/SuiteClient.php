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
use SmartAssert\SourcesClient\Exception\ModifyReadOnlyEntityException;
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
     * @throws UnauthorizedException
     */
    public function create(string $token, string $sourceId, string $label, array $tests): Suite
    {
        return $this->makeMutationRequest($token, new SuiteRequest('POST', $sourceId, $label, $tests));
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
     * @throws ModifyReadOnlyEntityException
     * @throws UnauthorizedException
     */
    public function update(string $token, string $suiteId, string $sourceId, string $label, array $tests): Suite
    {
        try {
            return $this->makeMutationRequest($token, new SuiteRequest('PUT', $sourceId, $label, $tests, $suiteId));
        } catch (NonSuccessResponseException $e) {
            if (405 === $e->getCode()) {
                throw new ModifyReadOnlyEntityException($suiteId, 'suite');
            }

            throw $e;
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws UnauthorizedException
     */
    public function delete(string $token, string $suiteId): Suite
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSuiteRequest('DELETE', $token, $suiteId)
        );

        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

        $suite = $this->suiteFactory->create($response->getData());
        if (null === $suite) {
            throw InvalidModelDataException::fromJsonResponse(Suite::class, $response);
        }

        return $suite;
    }

    /**
     * @return Suite[]
     *
     * @throws ClientExceptionInterface
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     * @throws InvalidResponseTypeException
     * @throws HttpResponseExceptionInterface
     * @throws UnauthorizedException
     */
    public function list(string $token): array
    {
        $response = $this->serviceClient->sendRequest($this->requestFactory->createSuitesRequest($token));

        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

        $sources = [];

        foreach ($response->getData() as $sourceData) {
            if (is_array($sourceData)) {
                $suite = $this->suiteFactory->create($sourceData);

                if ($suite instanceof Suite) {
                    $sources[] = $suite;
                }
            }
        }

        return $sources;
    }

    /**
     * @param non-empty-string $token
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws UnauthorizedException
     */
    private function makeMutationRequest(string $token, RequestInterface $request): Suite
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSuiteRequest($request->getMethod(), $token, $request->getResourceId())
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
