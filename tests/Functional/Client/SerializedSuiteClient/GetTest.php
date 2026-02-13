<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\SerializedSuiteClient;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\SourcesClient\Exception\ResponseException;
use SmartAssert\SourcesClient\Model\MetaState;
use SmartAssert\SourcesClient\Model\SerializedSuite;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;
use Symfony\Component\Uid\Ulid;

class GetTest extends AbstractSuiteClientTestCase
{
    use InvalidJsonResponseExceptionDataProviderTrait;
    use NetworkErrorExceptionDataProviderTrait;

    public function testGetThrowsInvalidModelDataException(): void
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
            $this->serializedSuiteClient->get(self::API_KEY, md5((string) rand()));
        };
    }

    protected function getExpectedRequestMethod(): string
    {
        return 'GET';
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
            ])
        );
    }

    /**
     * @param array<mixed> $responseData
     */
    #[DataProvider('getSuccessDataProvider')]
    public function testGetSuccess(array $responseData, SerializedSuite $expected): void
    {
        $this->mockHandler->append(new Response(
            200,
            ['content-type' => 'application/json'],
            (string) json_encode($responseData)
        ));

        $serializedSuite = $this->serializedSuiteClient->get('api key', md5((string) rand()));

        self::assertEquals($expected, $serializedSuite);
    }

    /**
     * @return array<mixed>
     */
    public static function getSuccessDataProvider(): array
    {
        $serializedSuiteId = (string) new Ulid();
        $suiteId = (string) new Ulid();

        return [
            'requested, no parameters' => [
                'responseData' => [
                    'id' => $serializedSuiteId,
                    'suite_id' => $suiteId,
                    'parameters' => [],
                    'state' => 'requested',
                    'meta_state' => [
                        'ended' => false,
                        'succeeded' => false,
                    ],
                ],
                'expected' => new SerializedSuite(
                    $serializedSuiteId,
                    $suiteId,
                    [],
                    'requested',
                    new MetaState(false, false),
                    null,
                    null,
                ),
            ],
            'requested, has parameters' => [
                'responseData' => [
                    'id' => $serializedSuiteId,
                    'suite_id' => $suiteId,
                    'parameters' => [
                        'parameter1' => 'value1',
                        'parameter2' => 'value2',
                    ],
                    'state' => 'requested',
                    'meta_state' => [
                        'ended' => false,
                        'succeeded' => false,
                    ],
                ],
                'expected' => new SerializedSuite(
                    $serializedSuiteId,
                    $suiteId,
                    [
                        'parameter1' => 'value1',
                        'parameter2' => 'value2',
                    ],
                    'requested',
                    new MetaState(false, false),
                    null,
                    null,
                ),
            ],
            'prepared' => [
                'responseData' => [
                    'id' => $serializedSuiteId,
                    'suite_id' => $suiteId,
                    'parameters' => [],
                    'state' => 'requested',
                    'meta_state' => [
                        'ended' => true,
                        'succeeded' => true,
                    ],
                ],
                'expected' => new SerializedSuite(
                    $serializedSuiteId,
                    $suiteId,
                    [],
                    'requested',
                    new MetaState(true, true),
                    null,
                    null,
                ),
            ],
        ];
    }
}
