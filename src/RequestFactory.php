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

    public function createReadSerializedSuiteRequest(string $token, string $serializedSuiteId): Request
    {
        return $this->doCreate(
            $token,
            'GET',
            $this->urlGenerator->generate('serialized_suite_read', ['serializedSuiteId' => $serializedSuiteId]),
        );
    }

    public function createSuiteSerializationRequest(string $token, string $serializedSuiteId, string $suiteId): Request
    {
        return $this->doCreate(
            $token,
            'POST',
            $this->urlGenerator->generate(
                'suite_serialize',
                [
                    'serializedSuiteId' => $serializedSuiteId,
                    'suiteId' => $suiteId
                ]
            )
        );
    }

    public function createSerializedSuiteRequest(string $token, string $serializedSuiteId): Request
    {
        return $this->doCreate(
            $token,
            'GET',
            $this->urlGenerator->generate('serialized_suite', ['serializedSuiteId' => $serializedSuiteId])
        );
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

        $routeCollection->add('suite_serialize', new Route('/suite/{suiteId}/{serializedSuiteId}'));
        $routeCollection->add('serialized_suite', new Route('/serialized_suite/{serializedSuiteId}'));
        $routeCollection->add('serialized_suite_read', new Route('/serialized_suite/{serializedSuiteId}/read'));

        return new UrlGenerator($routeCollection, new RequestContext($baseUrl));
    }
}
