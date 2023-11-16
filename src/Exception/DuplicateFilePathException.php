<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Exception;

use Psr\Http\Message\ResponseInterface;

class DuplicateFilePathException extends ResponseException
{
    public function __construct(
        public readonly string $path,
        ResponseInterface $response,
        string $type,
        array $payload
    ) {
        parent::__construct($response, $type, $payload);
    }
}
