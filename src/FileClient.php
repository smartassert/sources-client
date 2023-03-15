<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\Response\Response;

class FileClient
{
    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $fileSourceId
     * @param non-empty-string $filename
     *
     * @throws HttpResponseExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function add(string $token, string $fileSourceId, string $filename, string $content): void
    {
        $this->handleResponse($this->serviceClient->sendRequestForJsonEncodedData(
            $this->createRequest('POST', $token, $fileSourceId, $filename)
                ->withPayload(new Payload('text/x-yaml', $content))
        ));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws NonSuccessResponseException
     * @throws HttpResponseExceptionInterface
     */
    public function read(string $token, string $fileSourceId, string $filename): string
    {
        return $this->handleResponse($this->serviceClient->sendRequest(
            $this->createRequest('GET', $token, $fileSourceId, $filename)
        ));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws NonSuccessResponseException
     * @throws HttpResponseExceptionInterface
     */
    public function remove(string $token, string $fileSourceId, string $filename): void
    {
        $this->handleResponse($this->serviceClient->sendRequest(
            $this->createRequest('DELETE', $token, $fileSourceId, $filename)
        ));
    }

    private function createRequest(string $method, string $token, string $fileSourceId, string $filename): Request
    {
        return $this->requestFactory->createFileRequest($method, $token, $fileSourceId, $filename);
    }

    /**
     * @throws NonSuccessResponseException
     * @throws HttpResponseExceptionInterface
     */
    private function handleResponse(Response $response): string
    {
        if (!$response->isSuccessful()) {
            throw $this->exceptionFactory->createFromResponse($response);
        }

        return $response->getHttpResponse()->getBody()->getContents();
    }
}
