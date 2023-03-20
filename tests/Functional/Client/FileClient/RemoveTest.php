<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\FileClient;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class RemoveTest extends AbstractFileClientTest
{
    use NetworkErrorExceptionDataProviderTrait;

    public function clientActionThrowsExceptionDataProvider(): array
    {
        return $this->networkErrorExceptionDataProvider();
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->fileClient->remove(self::API_KEY, 'source_id', 'test.yaml');
        };
    }

    protected function getExpectedRequestMethod(): string
    {
        return 'DELETE';
    }

    protected function getClientActionSuccessResponse(): ResponseInterface
    {
        return new Response();
    }
}
