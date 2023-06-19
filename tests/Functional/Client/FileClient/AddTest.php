<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\FileClient;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\SourcesClient\Exception\FilesystemException;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class AddTest extends AbstractFileClientTestCase
{
    use NetworkErrorExceptionDataProviderTrait;

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
            $this->fileClient->add($apiKey, $fileSourceId, $filename, $content);
        } catch (\Throwable $e) {
            self::assertEquals($expected, $e);
        }
    }

    /**
     * @return array<mixed>
     */
    public static function addFileThrowsExceptionDataProvider(): array
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

    public static function clientActionThrowsExceptionDataProvider(): array
    {
        return static::networkErrorExceptionDataProvider();
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->fileClient->add(self::API_KEY, 'source_id', 'test.yaml', 'content');
        };
    }

    protected function getExpectedRequestMethod(): string
    {
        return 'POST';
    }

    protected function getClientActionSuccessResponse(): ResponseInterface
    {
        return new Response(200);
    }
}
