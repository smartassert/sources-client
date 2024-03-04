<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\CurlExceptionInterface;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Request;
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
        $request = $this->requestFactory->createFileRequest(
            new FileRequest('POST', $fileSourceId, $filename),
            $token
        )->withPayload(new Payload('text/x-yaml', $content));

        $this->handleRequest($request);
    }

    public function remove(string $token, string $fileSourceId, string $filename): void
    {
        $request = $this->requestFactory->createFileRequest(
            new FileRequest('DELETE', $fileSourceId, $filename),
            $token
        );

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
