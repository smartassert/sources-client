<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client;

use GuzzleHttp\Psr7\Response;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;

abstract class AbstractClientModelCreationTestCase extends AbstractClientTestCase
{
    public function testClientActionThrowsInvalidModelDataException(): void
    {
        $this->doClientActionThrowsInvalidModelDataException(
            200,
            $this->createClientActionCallable(),
            $this->getExpectedModelClass(),
        );
    }

    /**
     * @param class-string $expectedModelClass
     */
    protected function doClientActionThrowsInvalidModelDataException(
        int $responseStatusCode,
        callable $callable,
        string $expectedModelClass
    ): void {
        $responsePayload = ['key' => 'value'];
        $response = new Response(
            $responseStatusCode,
            ['content-type' => 'application/json'],
            (string) json_encode($responsePayload)
        );

        $this->mockHandler->append($response);

        try {
            $callable();
            self::fail(InvalidModelDataException::class . ' not thrown');
        } catch (InvalidModelDataException $e) {
            self::assertSame($expectedModelClass, $e->class);
            self::assertSame($response, $e->response);
            self::assertSame($responsePayload, $e->payload);
        }
    }

    /**
     * @return class-string
     */
    abstract protected function getExpectedModelClass(): string;
}
