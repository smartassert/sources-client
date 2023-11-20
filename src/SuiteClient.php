<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\SourcesClient\Exception\ModifyReadOnlyEntityException;
use SmartAssert\SourcesClient\Model\Suite;
use SmartAssert\SourcesClient\Request\RequestInterface;
use SmartAssert\SourcesClient\Request\SuiteCreationRequest;
use SmartAssert\SourcesClient\Request\SuiteMutationRequest;

class SuiteClient implements SuiteClientInterface
{
    use VerifyJsonResponseTrait;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SuiteFactory $suiteFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    public function create(string $token, string $sourceId, string $label, array $tests): Suite
    {
        return $this->makeMutationRequest($token, new SuiteCreationRequest($sourceId, $label, $tests));
    }

    public function get(string $token, string $suiteId): Suite
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSuiteRequest('GET', $token, $suiteId)
        );

        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

        $suite = $this->suiteFactory->create($response->getData());
        if (null === $suite) {
            throw InvalidModelDataException::fromJsonResponse(Suite::class, $response);
        }

        return $suite;
    }

    public function update(string $token, string $suiteId, string $sourceId, string $label, array $tests): Suite
    {
        try {
            return $this->makeMutationRequest($token, new SuiteMutationRequest($sourceId, $label, $tests, $suiteId));
        } catch (NonSuccessResponseException $e) {
            if (405 === $e->getCode()) {
                throw new ModifyReadOnlyEntityException($suiteId, 'suite');
            }

            throw $e;
        }
    }

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
