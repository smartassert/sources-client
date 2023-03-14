<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class UrlFactory
{
    private readonly string $baseUrl;

    public function __construct(
        string $baseUrl,
        private readonly UrlGenerator $urlGenerator
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public static function createUrlFactory(string $baseUrl): self
    {
        $fileRoute = new Route('/source/{sourceId}/{filename}');
        $sourcesRoute = new Route('/sources');
        $sourceRoute = new Route('/source/{sourceId?}');

        $routeCollection = new RouteCollection();

        $routeCollection->add('file', $fileRoute);
        $routeCollection->add('sources', $sourcesRoute);
        $routeCollection->add('source', $sourceRoute);

        $urlGenerator = new UrlGenerator($routeCollection, new RequestContext());

        return new UrlFactory($baseUrl, $urlGenerator);
    }

    public function createFileUrl(string $fileSourceId, string $filename): string
    {
        return $this->create('file', ['sourceId' => $fileSourceId, 'filename' => $filename]);
    }

    public function createSourceUrl(?string $sourceId): string
    {
        return $this->create('source', ['sourceId' => $sourceId]);
    }

    public function createSourcesUrl(): string
    {
        return $this->create('sources');
    }

    /**
     * @param array<string, null|int|string> $parameters
     */
    private function create(string $routeName, array $parameters = []): string
    {
        return $this->baseUrl . $this->urlGenerator->generate($routeName, $parameters);
    }
}
