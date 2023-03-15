<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\SourceClient;

use GuzzleHttp\Psr7\Response;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\SourcesClient\Exception\ResponseException;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class CreateGitSourceTest extends AbstractSourceClientTest
{
    use InvalidJsonResponseExceptionDataProviderTrait;
    use NetworkErrorExceptionDataProviderTrait;

    public function testCreateGitSourceRequestProperties(): void
    {
        $label = 'git source label';
        $hostUrl = 'https://example.com/repo.git';
        $path = '/';

        $this->mockHandler->append(new Response(
            200,
            ['content-type' => 'application/json'],
            (string) json_encode([
                'type' => 'git',
                'id' => md5((string) rand()),
                'user_id' => md5((string) rand()),
                'label' => $label,
                'host_url' => $hostUrl,
                'path' => $path,
                'has_credentials' => false,
            ])
        ));

        $apiKey = 'api key value';

        $this->sourceClient->createGitSource($apiKey, $label, $hostUrl, $path, null);

        $request = $this->getLastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('Bearer ' . $apiKey, $request->getHeaderLine('authorization'));
    }

    public function testCreateFileSourceThrowsInvalidModelDataException(): void
    {
        $responsePayload = ['key' => 'value'];
        $response = new Response(
            400,
            ['content-type' => 'application/json'],
            (string) json_encode($responsePayload)
        );

        $this->mockHandler->append($response);

        try {
            ($this->createClientActionCallable())();
            self::fail(InvalidModelDataException::class . ' not thrown');
        } catch (InvalidModelDataException $e) {
            self::assertSame(ResponseException::class, $e->class);
            self::assertSame($response, $e->response);
            self::assertSame($responsePayload, $e->payload);
        }
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
            $this->sourceClient->createGitSource('api token', 'git source label', 'host url', 'path', null);
        };
    }

    protected function getExpectedModelClass(): string
    {
        return SourceInterface::class;
    }
}
