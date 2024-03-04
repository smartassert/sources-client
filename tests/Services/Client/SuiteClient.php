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

readonly class SuiteClient
{
    public function __construct(
        private ServiceClient $serviceClient,
        private string $baseUrl,
    ) {
    }

    /**
     * @param non-empty-string $token
     * @param string[]         $tests
     *
     * @return non-empty-string
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws UnauthorizedException
     */
    public function create(string $token, string $sourceId, string $label, array $tests): ?string
    {
        $serviceRequest = (new Request('POST', $this->baseUrl . '/suite'))
            ->withPayload(new UrlEncodedPayload(['source_id' => $sourceId, 'label' => $label, 'tests' => $tests]))
            ->withAuthentication(new BearerAuthentication($token))
        ;

        $response = $this->serviceClient->sendRequestForJson($serviceRequest);
        $responseData = $response->getData();

        $id = $responseData['id'] ?? '';
        $id = is_string($id) ? $id : '';

        return '' === $id ? null : $id;
    }
}
