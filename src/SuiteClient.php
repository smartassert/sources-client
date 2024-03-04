<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\ServiceClient\Request;
use SmartAssert\SourcesClient\Model\Suite;
use SmartAssert\SourcesClient\Request\RequestInterface;
use SmartAssert\SourcesClient\Request\SuiteCreationRequest;

class SuiteClient
{
    public function __construct(
        private readonly ServiceClient $serviceClient,
        private readonly SuiteFactory $suiteFactory,
        private readonly ExceptionFactory $exceptionFactory,
        private readonly string $baseUrl,
    ) {
    }

    /**
     * @param non-empty-string $token
     * @param string[]         $tests
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws UnauthorizedException
     */
    public function create(string $token, string $sourceId, string $label, array $tests): Suite
    {
        return $this->makeMutationRequest($token, new SuiteCreationRequest($sourceId, $label, $tests));
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
        $serviceRequest = (new Request('POST', $this->baseUrl . '/suite'))
            ->withPayload(new UrlEncodedPayload($request->getPayload()))
            ->withAuthentication(new BearerAuthentication($token))
        ;

        try {
            $response = $this->serviceClient->sendRequestForJson($serviceRequest);
        } catch (NonSuccessResponseException $e) {
            throw $this->exceptionFactory->createFromResponse($e->getResponse());
        }

        $suite = $this->suiteFactory->create($response->getData());
        if (null === $suite) {
            throw InvalidModelDataException::fromJsonResponse(Suite::class, $response);
        }

        return $suite;
    }
}
