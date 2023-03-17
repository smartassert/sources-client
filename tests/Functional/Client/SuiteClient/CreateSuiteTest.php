<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client\SuiteClient;

use GuzzleHttp\Psr7\Response;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\SourcesClient\Exception\ResponseException;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

class CreateSuiteTest extends AbstractSuiteClientTest
{
    use InvalidJsonResponseExceptionDataProviderTrait;
    use NetworkErrorExceptionDataProviderTrait;

    public function testCreateRequestProperties(): void
    {
        $label = md5((string) rand());
        $sourceId = md5((string) rand());
        $tests = [
            md5((string) rand()) . '.yaml',
            md5((string) rand()) . '.yaml',
        ];

        $this->mockHandler->append(new Response(
            200,
            ['content-type' => 'application/json'],
            (string) json_encode([
                'id' => md5((string) rand()),
                'source_id' => $sourceId,
                'label' => $label,
                'tests' => $tests,
            ])
        ));

        $apiKey = 'api key value';

        $this->suiteClient->create($apiKey, $sourceId, $label, $tests);

        $request = $this->getLastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('Bearer ' . $apiKey, $request->getHeaderLine('authorization'));
    }

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
            $this->suiteClient->create('api token', 'source id', 'label', ['test.yaml']);
        };
    }
}
