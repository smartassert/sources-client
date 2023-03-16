<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\RequestFactory\AuthenticationMiddleware;
use SmartAssert\ServiceClient\RequestFactory\RequestFactory as ServiceClientRequestFactory;
use SmartAssert\ServiceClient\RequestFactory\RequestMiddlewareCollection;

class RequestFactory extends ServiceClientRequestFactory
{
    private readonly AuthenticationMiddleware $authenticationMiddleware;

    public function __construct(
        private readonly UrlFactory $urlFactory,
    ) {
        $this->authenticationMiddleware = new AuthenticationMiddleware();

        parent::__construct(
            (new RequestMiddlewareCollection())->set('authentication', $this->authenticationMiddleware)
        );
    }

    /**
     * @param non-empty-string $method
     */
    public function createFileRequest(string $method, string $token, string $fileSourceId, string $filename): Request
    {
        $this->authenticationMiddleware->setAuthentication(new BearerAuthentication($token));

        return $this->create($method, $this->urlFactory->createFileUrl($fileSourceId, $filename));
    }

    /**
     * @param non-empty-string $method
     */
    public function createSourceRequest(string $method, string $token, ?string $sourceId): Request
    {
        $this->authenticationMiddleware->setAuthentication(new BearerAuthentication($token));

        return $this->create($method, $this->urlFactory->createSourceUrl($sourceId));
    }

    public function createSourcesRequest(string $token): Request
    {
        $this->authenticationMiddleware->setAuthentication(new BearerAuthentication($token));

        return $this->create('GET', $this->urlFactory->createSourcesUrl());
    }

    public function createSourceFilenamesRequest(string $token, string $fileSourceId): Request
    {
        $this->authenticationMiddleware->setAuthentication(new BearerAuthentication($token));

        return $this->create('GET', $this->urlFactory->createSourceFilenamesUrl($fileSourceId));
    }
}
