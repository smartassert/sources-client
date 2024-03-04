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

readonly class FileSourceClient
{
    public function __construct(private ServiceClient $serviceClient, private string $baseUrl)
    {
    }

    /**
     * @param non-empty-string $token
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
    public function create(string $token, string $label): ?string
    {
        $serviceRequest = (new Request('POST', $this->baseUrl . '/file-source'))
            ->withPayload(new UrlEncodedPayload(['type' => 'file', 'label' => $label]))
            ->withAuthentication(new BearerAuthentication($token))
        ;

        $response = $this->serviceClient->sendRequestForJson($serviceRequest);
        $responseData = $response->getData();

        $id = $responseData['id'] ?? '';
        $id = is_string($id) ? $id : '';

        return '' === $id ? null : $id;
    }
}
