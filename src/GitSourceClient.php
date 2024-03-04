<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\CurlExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Request\GitSourceRequest;
use SmartAssert\SourcesClient\Request\RequestInterface;

class GitSourceClient
{
    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SourceFactory $sourceFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @param non-empty-string  $token
     * @param ?non-empty-string $credentials
     *
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
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
    ): GitSource {
        return $this->handleRequest(
            new GitSourceRequest('POST', $label, $hostUrl, $path, $credentials),
            $token
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     * @throws UnauthorizedException
     */
    private function handleRequest(RequestInterface $request, string $token): GitSource
    {
        try {
            $response = $this->serviceClient->sendRequestForJson(
                $this->requestFactory
                    ->createSourceRequest($request, $token)
                    ->withPayload(new UrlEncodedPayload($request->getPayload()))
            );
        } catch (NonSuccessResponseException $e) {
            throw $this->exceptionFactory->createFromResponse($e->getResponse());
        }

        $source = $this->sourceFactory->createGitSource($response->getData());
        if (null === $source) {
            throw InvalidModelDataException::fromJsonResponse(SourceInterface::class, $response);
        }

        return $source;
    }
}
