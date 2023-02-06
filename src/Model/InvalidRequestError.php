<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

use Psr\Http\Message\ResponseInterface;

class InvalidRequestError
{
    /**
     * @param array<non-empty-string, InvalidRequestField> $invalidRequestFields
     */
    public function __construct(
        public readonly ResponseInterface $httpResponse,
        public readonly array $invalidRequestFields,
    ) {
    }
}
