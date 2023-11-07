<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Response\ResponseInterface;
use SmartAssert\SourcesClient\Request\FileRequest;

readonly class FileClient
{
    public function __construct(
        private RequestFactory $requestFactory,
        private ServiceClient $serviceClient,
        private ExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $fileSourceId
     * @param non-empty-string $filename
     *
     * @throws HttpResponseExceptionInterface
     * @throws ClientExceptionInterface
     * @throws UnauthorizedException
     */
    public function add(string $token, string $fileSourceId, string $filename, string $content): void
    {
        $this->handleResponse($this->serviceClient->sendRequest(
            $this->requestFactory->createFileRequest(
                new FileRequest('POST', $fileSourceId, $filename),
                $token
            )->withPayload(new Payload('text/x-yaml', $content))
        ));
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $fileSourceId
     * @param non-empty-string $filename
     *
     * @throws ClientExceptionInterface
     * @throws NonSuccessResponseException
     * @throws HttpResponseExceptionInterface
     * @throws UnauthorizedException
     */
    public function read(string $token, string $fileSourceId, string $filename): string
    {
        return $this->handleResponse($this->serviceClient->sendRequest(
            $this->requestFactory->createFileRequest(
                new FileRequest('GET', $fileSourceId, $filename),
                $token
            )
        ));
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $fileSourceId
     * @param non-empty-string $filename
     *
     * @throws ClientExceptionInterface
     * @throws NonSuccessResponseException
     * @throws HttpResponseExceptionInterface
     * @throws UnauthorizedException
     */
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
     * @throws NonSuccessResponseException
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
