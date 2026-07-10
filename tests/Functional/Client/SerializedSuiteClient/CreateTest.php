<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\SerializedSuiteClient;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\SourcesClient\Exception\ResponseException;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class CreateTest extends AbstractSuiteClientTestCase
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
            self::assertSame($response, $e->getHttpResponse());
            self::assertSame($responsePayload, $e->payload);
        }
    }

    public function testNotifyUrlIsProvidedInRequestBody(): void
    {
        $this->mockHandler->append(
            $this->getClientActionSuccessResponse(),
        );

        $notifyUrl = 'https://' . md5((string) rand()) . '.com/' . md5((string) rand());

        $this->serializedSuiteClient->create(
            token: self::API_KEY,
            serializedSuiteId: md5((string) rand()),
            suiteId: ((string) rand()),
            notifyUrl: $notifyUrl,
        );

        $lastRequestBody = $this->getLastRequest()->getBody()->getContents();
        $decodedRequestBody = urldecode($lastRequestBody);

        self::assertSame(
            'notify_url=' . $notifyUrl,
            $decodedRequestBody,
        );
    }

    public static function clientActionThrowsExceptionDataProvider(): array
    {
        return array_merge(
            static::networkErrorExceptionDataProvider(),
            static::invalidJsonResponseExceptionDataProvider(),
        );
    }

    protected function createClientActionCallable(): callable
    {
        return function () {
            $this->serializedSuiteClient->create(
                token: self::API_KEY,
                serializedSuiteId: md5((string) rand()),
                suiteId: ((string) rand()),
                notifyUrl: 'https://example.com/notify',
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
                'suite_id' => md5((string) rand()),
                'parameters' => [],
                'state' => md5((string) rand()),
                'meta_state' => [
                    'ended' => false,
                    'succeeded' => false,
                ],
                'previous_states' => [],
                'next_states' => [
                    md5((string) rand()),
                    md5((string) rand()),
                    md5((string) rand()),
                ],
            ])
        );
    }
}
