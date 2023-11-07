<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class SuiteRequest implements RequestInterface
{
    /**
     * @param 'DELETE'|'GET'|'POST'|'PUT' $method
     * @param non-empty-string            $sourceId
     * @param non-empty-string            $label
     * @param non-empty-string[]          $tests
     * @param null|non-empty-string       $resourceId
     */
    public function __construct(
        private readonly string $method,
        private readonly string $sourceId,
        private readonly string $label,
        private readonly array $tests,
        private readonly ?string $resourceId = null,
    ) {
    }

    public function hasResourceId(): bool
    {
        return is_string($this->resourceId);
    }

    /**
     * @return null|non-empty-string
     */
    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }

    public function getPayload(): array
    {
        return ['source_id' => $this->sourceId, 'label' => $this->label, 'tests' => $this->tests];
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
