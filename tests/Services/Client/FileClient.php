<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Services\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\CurlExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Request;
use SmartAssert\SourcesClient\ExceptionFactory;

readonly class FileClient
{
    public function __construct(
        private ServiceClient $serviceClient,
        private ExceptionFactory $exceptionFactory,
        private string $baseUrl,
    ) {
    }

    public function add(string $token, string $fileSourceId, string $filename, string $content): void
    {
        $request = (
            new Request(
                'POST',
                $this->baseUrl . '/file-source/' . $fileSourceId . '/' . $filename
            )
        )
            ->withPayload(new Payload('text/x-yaml', $content))
            ->withAuthentication(new BearerAuthentication($token))
        ;

        $this->handleRequest($request);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidResponseDataException
     * @throws NetworkExceptionInterface
     * @throws RequestExceptionInterface
     * @throws UnauthorizedException
     */
    private function handleRequest(Request $request): string
    {
        try {
            $response = $this->serviceClient->sendRequest($request);
        } catch (NonSuccessResponseException $e) {
            throw $this->exceptionFactory->createFromResponse($e->getResponse());
        }

        return $response->getHttpResponse()->getBody()->getContents();
    }
}
