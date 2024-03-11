<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

readonly class SerializedSuite implements SerializedSuiteInterface
{
    /**
     * @param non-empty-string      $id
     * @param non-empty-string      $suiteId
     * @param array<string, string> $parameters
     * @param non-empty-string      $state
     */
    public function __construct(
        private string $id,
        private string $suiteId,
        private array $parameters,
        private string $state,
        private ?string $failureReason,
        private ?string $failureMessage,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSuiteId(): string
    {
        return $this->suiteId;
    }

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

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'suite_id' => $this->suiteId,
            'parameters' => $this->parameters,
            'state' => $this->state,
        ];

        if (is_string($this->failureReason)) {
            $data['failure_reason'] = $this->failureReason;
        }

        if (is_string($this->failureMessage)) {
            $data['failure_message'] = $this->failureMessage;
        }

        return $data;
    }
}
