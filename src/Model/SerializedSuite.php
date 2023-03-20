<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class SerializedSuite
{
    /**
     * @param non-empty-string      $id
     * @param non-empty-string      $suiteId
     * @param array<string, string> $parameters
     * @param non-empty-string      $state
     */
    public function __construct(
        private readonly string $id,
        private readonly string $suiteId,
        private readonly array $parameters,
        private readonly string $state,
        private readonly ?string $failureReason,
        private readonly ?string $failureMessage,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function getSuiteId(): string
    {
        return $this->suiteId;
    }

    /**
     * @return array<string, string>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getFailureReason(): ?string
    {
        return $this->failureReason;
    }

    public function getFailureMessage(): ?string
    {
        return $this->failureMessage;
    }
}
