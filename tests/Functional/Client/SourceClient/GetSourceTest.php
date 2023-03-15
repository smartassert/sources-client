<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\SourceClient;

use GuzzleHttp\Psr7\Response;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class GetSourceTest extends AbstractSourceClientTest
{
    use InvalidJsonResponseExceptionDataProviderTrait;
    use NetworkErrorExceptionDataProviderTrait;

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

        $this->sourceClient->get($apiKey, $sourceId);

        $request = $this->getLastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('Bearer ' . $apiKey, $request->getHeaderLine('authorization'));
    }

    public function clientActionThrowsExceptionDataProvider(): array
    {
        return array_merge(
            $this->networkErrorExceptionDataProvider(),
            $this->invalidJsonResponseExceptionDataProvider(),
        );
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->sourceClient->get('api token', 'source id');
        };
    }

    protected function getExpectedModelClass(): string
    {
        return SourceInterface::class;
    }
}
