<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

use Psr\Http\Message\ResponseInterface;

interface ErrorInterface
{
    public function getHttpResponse(): ResponseInterface;

    public function getType(): string;

    /**
     * @return array<mixed>
     */
    public function getPayload(): array;
}
