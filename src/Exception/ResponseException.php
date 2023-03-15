<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Exception;

use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Exception\AbstractInvalidResponseException;

class ResponseException extends AbstractInvalidResponseException implements ResponseExceptionInterface
{
    /**
     * @param array<mixed> $payload
     */
    public function __construct(
        ResponseInterface $response,
        private readonly string $type,
        private readonly array $payload,
    ) {
        parent::__construct($response, $type);
    }

    public function getPayloadStringValue(string $key): string
    {
        $value = $this->payload[$key] ?? '';
        if (!is_string($value)) {
            $value = '';
        }

        return $value;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
