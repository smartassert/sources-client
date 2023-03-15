<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client;

use GuzzleHttp\Psr7\Response;
use SmartAssert\SourcesClient\Model\SourceInterface;

class GetSourceTest extends AbstractClientModelCreationTestCase
{
    use NetworkErrorExceptionAndInvalidJsonResponseExceptionDataProviderTrait;

    public function testGetSourceRequestProperties(): void
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

        $this->client->sourceHandler->get($apiKey, $sourceId);

        $request = $this->getLastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('Bearer ' . $apiKey, $request->getHeaderLine('authorization'));
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->client->sourceHandler->get('api token', 'source id');
        };
    }

    protected function getExpectedModelClass(): string
    {
        return SourceInterface::class;
    }
}
