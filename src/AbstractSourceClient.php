<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ServiceClient\Client as ServiceClient;

abstract class AbstractSourceClient
{
    public function __construct(
        protected readonly RequestFactory $requestFactory,
        protected readonly ServiceClient $serviceClient,
        protected readonly SourceFactory $sourceFactory,
        protected readonly ExceptionFactory $exceptionFactory,
    ) {
    }
}
