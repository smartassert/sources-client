<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Request;

class RequestFactory
{
    public function __construct(
        private readonly UrlFactory $urlFactory,
    ) {
    }

    public function createFileRequest(string $method, string $token, string $fileSourceId, string $filename): Request
    {
        return $this->createRequest($method, $this->urlFactory->createFileUrl($fileSourceId, $filename), $token);
    }

    public function createSourceRequest(string $method, string $token, ?string $sourceId): Request
    {
        return $this->createRequest($method, $this->urlFactory->createSourceUrl($sourceId), $token);
    }

    public function createSourcesRequest(string $token): Request
    {
        return $this->createRequest('GET', $this->urlFactory->createSourcesUrl(), $token);
    }

    public function createRequest(string $method, string $url, string $token): Request
    {
        return (new Request($method, $url))
            ->withAuthentication(new BearerAuthentication($token))
        ;
    }
}
