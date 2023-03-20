<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\SuiteClient;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\SourcesClient\Exception\ResponseException;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class CreateTest extends AbstractSuiteClientTest
{
    use InvalidJsonResponseExceptionDataProviderTrait;
    use NetworkErrorExceptionDataProviderTrait;

    public function testCreateThrowsInvalidModelDataException(): void
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
            $this->suiteClient->create(
                self::API_KEY,
                md5((string) rand()),
                md5((string) rand()),
                [md5((string) rand()) . '.yaml']
            );
        };
    }

    protected function getExpectedRequestMethod(): string
    {
        return 'POST';
    }

    protected function getClientActionSuccessResponse(): ResponseInterface
    {
        return new Response(
            200,
            ['content-type' => 'application/json'],
            (string) json_encode([
                'id' => md5((string) rand()),
                'source_id' => md5((string) rand()),
                'label' => md5((string) rand()),
                'tests' => [md5((string) rand()) . '.yaml'],
            ])
        );
    }
}
