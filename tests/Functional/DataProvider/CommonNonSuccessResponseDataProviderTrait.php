<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\DataProvider;

use GuzzleHttp\Psr7\Response;

trait CommonNonSuccessResponseDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public static function commonNonSuccessResponseDataProvider(): array
    {
        return [
            'Internal server error' => [
                'httpFixture' => new Response(500),
            ],
        ];
    }
}
