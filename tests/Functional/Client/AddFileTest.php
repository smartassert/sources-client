<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\SourcesClient\Exception\FilesystemException;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class AddFileTest extends AbstractClientTestCase
{
    use NetworkErrorExceptionDataProviderTrait;

    public function testAddFileRequestProperties(): void
    {
        $this->mockHandler->append(new Response(200));

        $apiKey = 'api key value';
        $fileSourceId = md5((string) rand());
        $filename = 'test.yaml';
        $content = 'test file content';

        $this->client->addFile($apiKey, $fileSourceId, $filename, $content);

        $request = $this->getLastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('Bearer ' . $apiKey, $request->getHeaderLine('authorization'));
    }

    /**
     * @dataProvider addFileThrowsExceptionDataProvider
     */
    public function testAddFileThrowsException(ResponseInterface $response, \Throwable $expected): void
    {
        $this->mockHandler->append($response);

        $apiKey = 'api key value';
        $fileSourceId = md5((string) rand());
        $filename = 'test.yaml';
        $content = 'test file content';

        try {
            $this->client->addFile($apiKey, $fileSourceId, $filename, $content);
        } catch (\Throwable $e) {
            self::assertEquals($expected, $e);
        }
    }

    /**
     * @return array<mixed>
     */
    public function addFileThrowsExceptionDataProvider(): array
    {
        $filesystemWriteExceptionPayload = [
            'file' => 'file.txt',
            'message' => 'Unable to write file to disk'
        ];

        $filesystemWriteExceptionResponse = new Response(
            400,
            ['content-type' => 'application/json'],
            (string) json_encode([
                'error' => [
                    'type' => 'source_write_exception',
                    'payload' => $filesystemWriteExceptionPayload,
                ],
            ])
        );

        return [
            'write error' => [
                'response' => $filesystemWriteExceptionResponse,
                'expected' => new FilesystemException(
                    $filesystemWriteExceptionResponse,
                    'source_write_exception',
                    $filesystemWriteExceptionPayload
                ),
            ],
        ];
    }

    public function clientActionThrowsExceptionDataProvider(): array
    {
        return $this->networkErrorExceptionDataProvider();
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->client->addFile('api token', 'source_id', 'test.yaml', 'content');
        };
    }
}
