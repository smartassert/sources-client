<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Services\Client;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\CurlExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\ServiceClient\Request;

readonly class GitSourceClient
{
    public function __construct(private ServiceClient $serviceClient, private string $baseUrl)
    {
    }

    /**
     * @param non-empty-string  $token
     * @param ?non-empty-string $credentials
     *
     * @return ?non-empty-string
     *
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     * @throws UnauthorizedException
     */
    public function create(string $token, string $label, string $hostUrl, string $path, ?string $credentials): ?string
    {
        $payload = ['type' => 'git', 'label' => $label, 'host-url' => $hostUrl, 'path' => $path];
        if (is_string($credentials)) {
            $payload['credentials'] = $credentials;
        }

        $serviceRequest = (new Request('POST', $this->baseUrl . '/git-source'))
            ->withPayload(new UrlEncodedPayload($payload))
            ->withAuthentication(new BearerAuthentication($token))
        ;

        $response = $this->serviceClient->sendRequestForJson($serviceRequest);
        $responseData = $response->getData();

        $id = $responseData['id'] ?? '';
        $id = is_string($id) ? $id : '';

        return '' === $id ? null : $id;
    }
}
