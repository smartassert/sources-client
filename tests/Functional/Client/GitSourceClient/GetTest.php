<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\GitSourceClient;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class GetTest extends AbstractGitSourceClientTestCase
{
    use InvalidJsonResponseExceptionDataProviderTrait;
    use NetworkErrorExceptionDataProviderTrait;

    public function testGetRequestProperties(): void
    {
        $sourceId = md5((string) rand());

        $this->mockHandler->append(new Response(
            200,
            ['content-type' => 'application/json'],
            (string) json_encode([
                'id' => $sourceId,
                'type' => 'git',
                'user_id' => md5((string) rand()),
                'label' => 'source label',
                'host_url' => 'https://example.com/repo.git',
                'path' => '/',
                'has_credentials' => false,
            ])
        ));

        $apiKey = 'api key value';

        $this->gitSourceClient->get($apiKey, $sourceId);

        $request = $this->getLastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('Bearer ' . $apiKey, $request->getHeaderLine('authorization'));
    }

    public static function clientActionThrowsExceptionDataProvider(): array
    {
        return array_merge(
            static::networkErrorExceptionDataProvider(),
            static::invalidJsonResponseExceptionDataProvider(),
        );
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->gitSourceClient->get(self::API_KEY, md5((string) rand()));
        };
    }

    protected function getExpectedRequestMethod(): string
    {
        return 'GET';
    }

    protected function getClientActionSuccessResponse(): ResponseInterface
    {
        return new Response(
            200,
            ['content-type' => 'application/json'],
            (string) json_encode([
                'id' => md5((string) rand()),
                'type' => 'git',
                'user_id' => md5((string) rand()),
                'label' => 'source label',
                'host_url' => 'https://example.com/repo.git',
                'path' => '/',
                'has_credentials' => false,
            ])
        );
    }
}