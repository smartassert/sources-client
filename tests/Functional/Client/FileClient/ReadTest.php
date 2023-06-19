<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\FileClient;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class ReadTest extends AbstractFileClientTestCase
{
    use NetworkErrorExceptionDataProviderTrait;

    public static function clientActionThrowsExceptionDataProvider(): array
    {
        return static::networkErrorExceptionDataProvider();
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->fileClient->read(self::API_KEY, 'source_id', 'test.yaml');
        };
    }

    protected function getExpectedRequestMethod(): string
    {
        return 'GET';
    }

    protected function getClientActionSuccessResponse(): ResponseInterface
    {
        return new Response(200, [], 'file content');
    }
}
