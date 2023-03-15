<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client;

use GuzzleHttp\Psr7\Response;

class ListSourcesTest extends AbstractClientTestCase
{
    use NetworkErrorExceptionAndInvalidJsonResponseExceptionDataProviderTrait;

    public function testListSourcesRequestProperties(): void
    {
        $apiKey = 'api key value';

        $this->mockHandler->append(new Response(
            200,
            ['content-type' => 'application/json'],
            (string) json_encode([])
        ));

        $this->client->sourceHandler->list($apiKey);

        $request = $this->getLastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('Bearer ' . $apiKey, $request->getHeaderLine('authorization'));
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->client->sourceHandler->list('api token');
        };
    }
}
