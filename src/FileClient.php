<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Response\ResponseInterface;
use SmartAssert\SourcesClient\Request\FileRequest;

readonly class FileClient implements FileClientInterface
{
    public function __construct(
        private RequestFactory $requestFactory,
        private ServiceClient $serviceClient,
        private ExceptionFactory $exceptionFactory,
    ) {
    }

    public function add(string $token, string $fileSourceId, string $filename, string $content): void
    {
        $this->handleResponse($this->serviceClient->sendRequest(
            $this->requestFactory->createFileRequest(
                new FileRequest('POST', $fileSourceId, $filename),
                $token
            )->withPayload(new Payload('text/x-yaml', $content))
        ));
    }

    public function update(string $token, string $fileSourceId, string $filename, string $content): void
    {
        $this->handleResponse($this->serviceClient->sendRequest(
            $this->requestFactory->createFileRequest(
                new FileRequest('PUT', $fileSourceId, $filename),
                $token
            )->withPayload(new Payload('text/x-yaml', $content))
        ));
    }

    public function read(string $token, string $fileSourceId, string $filename): string
    {
        return $this->handleResponse($this->serviceClient->sendRequest(
            $this->requestFactory->createFileRequest(
                new FileRequest('GET', $fileSourceId, $filename),
                $token
            )
        ));
    }

    public function remove(string $token, string $fileSourceId, string $filename): void
    {
        $this->handleResponse($this->serviceClient->sendRequest(
            $this->requestFactory->createFileRequest(
                new FileRequest('DELETE', $fileSourceId, $filename),
                $token
            )
        ));
    }

    /**
     * @throws InvalidResponseDataException
     * @throws HttpResponseExceptionInterface
     */
    private function handleResponse(ResponseInterface $response): string
    {
        if (!$response->isSuccessful()) {
            throw $this->exceptionFactory->createFromResponse($response);
        }

        return $response->getHttpResponse()->getBody()->getContents();
    }
}
