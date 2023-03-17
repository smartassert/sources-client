<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class SuiteRequest implements RequestInterface
{
    /**
     * @param non-empty-string      $sourceId
     * @param non-empty-string      $label
     * @param non-empty-string[]    $tests
     * @param null|non-empty-string $id
     */
    public function __construct(
        private readonly string $sourceId,
        private readonly string $label,
        private readonly array $tests,
        private readonly ?string $id = null,
    ) {
    }

    public function hasId(): bool
    {
        return is_string($this->id);
    }

    /**
     * @return null|non-empty-string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPayload(): array
    {
        return ['source_id' => $this->sourceId, 'label' => $this->label, 'tests' => $this->tests];
    }
}
