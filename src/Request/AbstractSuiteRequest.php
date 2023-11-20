<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

abstract readonly class AbstractSuiteRequest implements RequestInterface
{
    /**
     * @param string[] $tests
     */
    public function __construct(
        private string $sourceId,
        private string $label,
        private array $tests,
    ) {
    }

    public function getPayload(): array
    {
        return ['source_id' => $this->sourceId, 'label' => $this->label, 'tests' => $this->tests];
    }

    public function getRoute(): string
    {
        return 'suite';
    }
}
