<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Exception;

use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface as HttpResponseException;
use SmartAssert\ServiceClient\Exception\HttpResponsePayloadExceptionInterface as HttpResponsePayloadException;

interface ResponseExceptionInterface extends \Throwable, HttpResponseException, HttpResponsePayloadException
{
    public function getType(): string;
}
