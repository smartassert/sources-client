<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractError implements ErrorInterface
{
    /**
     * @param array<mixed> $payload
     */
    public function __construct(
        private readonly ResponseInterface $httpResponse,
        private readonly string $type,
        private readonly array $payload,
    ) {
    }

    public function getHttpResponse(): ResponseInterface
    {
        return $this->httpResponse;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    protected function getPayloadStringValue(string $key): string
    {
        $value = $this->getPayload()[$key] ?? '';
        if (!is_string($value)) {
            $value = '';
        }

        return $value;
    }
}
