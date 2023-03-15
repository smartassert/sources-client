<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client;

use GuzzleHttp\Psr7\Response;
use SmartAssert\SourcesClient\Exception\ResponseException;
use SmartAssert\SourcesClient\Model\SourceInterface;

class CreateGitSourceTest extends AbstractClientModelCreationTestCase
{
    use NetworkErrorExceptionAndInvalidJsonResponseExceptionDataProviderTrait;

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

        $this->client->sourceClient->createGitSource($apiKey, $label, $hostUrl, $path, null);

        $request = $this->getLastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('Bearer ' . $apiKey, $request->getHeaderLine('authorization'));
    }

    public function testCreateGitSourceThrowsInvalidModelDataException(): void
    {
        $this->doClientActionThrowsInvalidModelDataException(
            400,
            $this->createClientActionCallable(),
            ResponseException::class,
        );
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->client->sourceClient->createGitSource('api token', 'git source label', 'host url', 'path', null);
        };
    }

    protected function getExpectedModelClass(): string
    {
        return SourceInterface::class;
    }
}
