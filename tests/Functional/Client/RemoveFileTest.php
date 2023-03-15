<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client;

use GuzzleHttp\Psr7\Response;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class RemoveFileTest extends AbstractClientTestCase
{
    use NetworkErrorExceptionDataProviderTrait;

    public function testRemoveFileRequestProperties(): void
    {
        $apiKey = 'api key value';
        $fileSourceId = md5((string) rand());
        $filename = 'test.yaml';
        $content = 'test file content';

        $this->mockHandler->append(new Response(200, [], $content));

        $this->client->fileClient->remove($apiKey, $fileSourceId, $filename);

        $request = $this->getLastRequest();
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('Bearer ' . $apiKey, $request->getHeaderLine('authorization'));
    }

    public function clientActionThrowsExceptionDataProvider(): array
    {
        return $this->networkErrorExceptionDataProvider();
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->client->fileClient->remove('api token', 'source_id', 'test.yaml');
        };
    }
}
