<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client;

use GuzzleHttp\Psr7\Response;
use SmartAssert\SourcesClient\Exception\ResponseException;
use SmartAssert\SourcesClient\Model\SourceInterface;

class CreateFileSourceTest extends AbstractClientModelCreationTestCase
{
    use NetworkErrorExceptionAndInvalidJsonResponseExceptionDataProviderTrait;

    public function testCreateFileSourceRequestProperties(): void
    {
        $label = 'job label';

        $this->mockHandler->append(new Response(
            200,
            ['content-type' => 'application/json'],
            (string) json_encode([
                'id' => md5((string) rand()),
                'user_id' => md5((string) rand()),
                'type' => 'file',
                'label' => $label,
            ])
        ));

        $apiKey = 'api key value';

        $this->client->sourceHandler->createFileSource($apiKey, $label);

        $request = $this->getLastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('Bearer ' . $apiKey, $request->getHeaderLine('authorization'));
    }

    public function testCreateFileSourceThrowsInvalidModelDataException(): void
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
            $this->client->sourceHandler->createFileSource('api token', 'label');
        };
    }

    protected function getExpectedModelClass(): string
    {
        return SourceInterface::class;
    }
}
