<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

readonly class SuiteMutationRequest extends AbstractSuiteRequest
{
    /**
     * @param string[]         $tests
     * @param non-empty-string $resourceId
     */
    public function __construct(
        string $sourceId,
        string $label,
        array $tests,
        private string $resourceId,
    ) {
        parent::__construct($sourceId, $label, $tests);
    }

    /**
     * @return null|non-empty-string
     */
    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }

    public function getMethod(): string
    {
        return 'PUT';
    }
}
