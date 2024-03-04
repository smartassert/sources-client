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
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Request\GitSourceRequest;
use SmartAssert\SourcesClient\SourceFactory;

readonly class GitSourceClient
{
    public function __construct(
        private ServiceClient $serviceClient,
        private SourceFactory $sourceFactory,
        private string $baseUrl,
    ) {
    }

    /**
     * @param non-empty-string  $token
     * @param ?non-empty-string $credentials
     *
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     * @throws UnauthorizedException
     */
    public function create(
        string $token,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
    ): ?GitSource {
        $request = new GitSourceRequest('POST', $label, $hostUrl, $path, $credentials);

        $serviceRequest = (new Request('POST', $this->baseUrl . '/git-source'))
            ->withPayload(new UrlEncodedPayload($request->getPayload()))
            ->withAuthentication(new BearerAuthentication($token))
        ;

        $response = $this->serviceClient->sendRequestForJson($serviceRequest);

        return $this->sourceFactory->createGitSource($response->getData());
    }
}
