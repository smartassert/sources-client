<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

readonly class SuiteCreationRequest extends AbstractSuiteRequest
{
    public function getResourceId(): null
    {
        return null;
    }

    public function getMethod(): string
    {
        return 'POST';
    }
}
