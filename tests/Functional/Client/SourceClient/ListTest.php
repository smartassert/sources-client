<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\SourceClient;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class ListTest extends AbstractSourceClientTestCase
{
    use InvalidJsonResponseExceptionDataProviderTrait;
    use NetworkErrorExceptionDataProviderTrait;

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
            $this->sourceClient->list(self::API_KEY);
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
            (string) json_encode([])
        );
    }
}
