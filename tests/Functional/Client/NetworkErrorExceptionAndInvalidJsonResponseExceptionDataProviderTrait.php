<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client;

use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;

trait NetworkErrorExceptionAndInvalidJsonResponseExceptionDataProviderTrait
{
    use InvalidJsonResponseExceptionDataProviderTrait;
    use NetworkErrorExceptionDataProviderTrait;

    public function clientActionThrowsExceptionDataProvider(): array
    {
        return array_merge(
            $this->networkErrorExceptionDataProvider(),
            $this->invalidJsonResponseExceptionDataProvider(),
        );
    }
}
