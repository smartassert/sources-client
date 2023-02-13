<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class RunSource extends AbstractSource
{
    /**
     * @param non-empty-string      $id
     * @param non-empty-string      $userId
     * @param string[]              $parameters
     * @param non-empty-string      $state
     * @param null|non-empty-string $parent
     */
    public function __construct(
        string $id,
        string $userId,
        private readonly array $parameters,
        private readonly string $state,
        private readonly ?string $parent,
        private readonly ?string $failureReason,
        private readonly ?string $failureMessage,
    ) {
        parent::__construct($id, $userId, 'run');
    }

    /**
     * @return string[]
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

    /**
     * @return ?non-empty-string
     */
    public function getParent(): ?string
    {
        return $this->parent;
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
