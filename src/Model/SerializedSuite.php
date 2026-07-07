<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

readonly class SerializedSuite
{
    /**
     * @param non-empty-string      $id
     * @param non-empty-string      $suiteId
     * @param array<string, string> $parameters
     * @param non-empty-string      $state
     * @param string[]              $previousStates
     * @param string[]              $nextStates
     */
    public function __construct(
        private string $id,
        private string $suiteId,
        private array $parameters,
        private string $state,
        private MetaState $metaState,
        private ?string $failureReason,
        private ?string $failureMessage,
        private array $previousStates,
        private array $nextStates,
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

    /**
     * @return non-empty-string
     */
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

    public function isPrepared(): bool
    {
        return $this->metaState->ended && $this->metaState->succeeded;
    }

    public function hasEndState(): bool
    {
        return $this->metaState->ended;
    }

    public function getMetaState(): MetaState
    {
        return $this->metaState;
    }

    /**
     * @return string[]
     */
    public function getPreviousStates(): array
    {
        return $this->previousStates;
    }

    /**
     * @return string[]
     */
    public function getNextStates(): array
    {
        return $this->nextStates;
    }
}
