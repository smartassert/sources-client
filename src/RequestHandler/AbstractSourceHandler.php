<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\RequestHandler;

use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\SourcesClient\ExceptionFactory;
use SmartAssert\SourcesClient\RequestFactory;
use SmartAssert\SourcesClient\SourceFactory;

abstract class AbstractSourceHandler
{
    public function __construct(
        protected readonly RequestFactory $requestFactory,
        protected readonly ServiceClient $serviceClient,
        protected readonly SourceFactory $sourceFactory,
        protected readonly ExceptionFactory $exceptionFactory,
    ) {
    }
}
