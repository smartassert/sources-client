<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\RequestFactory\AuthenticationMiddleware;
use SmartAssert\ServiceClient\RequestFactory\RequestFactory as ServiceClientRequestFactory;
use SmartAssert\ServiceClient\RequestFactory\RequestMiddlewareCollection;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RequestFactory extends ServiceClientRequestFactory
{
    private readonly AuthenticationMiddleware $authenticationMiddleware;
    private UrlGenerator $urlGenerator;

    public function __construct(string $baseUrl)
    {
        $this->authenticationMiddleware = new AuthenticationMiddleware();

        parent::__construct(
            (new RequestMiddlewareCollection())->set('authentication', $this->authenticationMiddleware)
        );

        $this->urlGenerator = $this->createUrlGenerator($baseUrl);
    }

    /**
     * @param non-empty-string $method
     */
    public function createFileRequest(string $method, string $token, string $fileSourceId, string $filename): Request
    {
        $url = $this->urlGenerator->generate('file', ['sourceId' => $fileSourceId, 'filename' => $filename]);

        return $this->doCreate($token, $method, $url);
    }

    /**
     * @param non-empty-string $method
     */
    public function createSourceRequest(string $method, string $token, ?string $sourceId): Request
    {
        return $this->doCreate($token, $method, $this->urlGenerator->generate('source', ['sourceId' => $sourceId]));
    }

    public function createSourcesRequest(string $token): Request
    {
        return $this->doCreate($token, 'GET', $this->urlGenerator->generate('sources'));
    }

    public function createSourceFilenamesRequest(string $token, string $fileSourceId): Request
    {
        $url = $this->urlGenerator->generate('source_filenames', ['sourceId' => $fileSourceId]);

        return $this->doCreate($token, 'GET', $url);
    }

    /**
     * @param non-empty-string $method
     */
    public function createSuiteRequest(string $method, string $token, ?string $suiteId): Request
    {
        return $this->doCreate($token, $method, $this->urlGenerator->generate('suite', ['suiteId' => $suiteId]));
    }

    public function createSuitesRequest(string $token): Request
    {
        return $this->doCreate($token, 'GET', $this->urlGenerator->generate('suites'));
    }

    /**
     * @param non-empty-string $method
     */
    private function doCreate(string $token, string $method, string $url): Request
    {
        $this->authenticationMiddleware->setAuthentication(new BearerAuthentication($token));

        return $this->create($method, $url);
    }

    private function createUrlGenerator(string $baseUrl): UrlGenerator
    {
        $routeCollection = new RouteCollection();

        $routeCollection->add('file', new Route('/source/{sourceId}/{filename}'));
        $routeCollection->add('sources', new Route('/sources'));
        $routeCollection->add('source', new Route('/source/{sourceId?}'));
        $routeCollection->add('source_filenames', new Route('/source/{sourceId}/list'));
        $routeCollection->add('suite', new Route('/suite/{suiteId?}'));
        $routeCollection->add('suites', new Route('/suites'));

        return new UrlGenerator($routeCollection, new RequestContext($baseUrl));
    }
}
