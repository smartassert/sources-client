<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class SuiteRequest implements RequestInterface
{
    /**
     * @param 'DELETE'|'GET'|'POST'|'PUT' $method
     * @param non-empty-string            $sourceId
     * @param string[]                    $tests
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

    public function getRoute(): string
    {
        return 'suite';
    }
}
