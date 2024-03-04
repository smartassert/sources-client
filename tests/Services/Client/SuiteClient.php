<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Services\Client;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\ServiceClient\Request;
use SmartAssert\SourcesClient\Model\Suite;
use SmartAssert\SourcesClient\Request\SuiteCreationRequest;
use SmartAssert\SourcesClient\SuiteFactory;

readonly class SuiteClient
{
    public function __construct(
        private ServiceClient $serviceClient,
        private SuiteFactory $suiteFactory,
        private string $baseUrl,
    ) {
    }

    /**
     * @param non-empty-string $token
     * @param string[]         $tests
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws UnauthorizedException
     */
    public function create(string $token, string $sourceId, string $label, array $tests): ?Suite
    {
        $request = new SuiteCreationRequest($sourceId, $label, $tests);

        $serviceRequest = (new Request('POST', $this->baseUrl . '/suite'))
            ->withPayload(new UrlEncodedPayload($request->getPayload()))
            ->withAuthentication(new BearerAuthentication($token))
        ;

        $response = $this->serviceClient->sendRequestForJson($serviceRequest);

        return $this->suiteFactory->create($response->getData());
    }
}
