<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\DataProvider;

use GuzzleHttp\Psr7\Response;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;

trait InvalidJsonResponseExceptionDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public static function invalidJsonResponseExceptionDataProvider(): array
    {
        return [
            'invalid response type' => [
                'httpFixture' => new Response(200, ['content-type' => 'text/plain']),
                'expectedExceptionClass' => InvalidResponseTypeException::class,
            ],
            'invalid response data' => [
                'httpFixture' => new Response(200, ['content-type' => 'application/json'], '1'),
                'expectedExceptionClass' => InvalidResponseDataException::class,
            ],
        ];
    }
}
