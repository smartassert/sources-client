<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

use SmartAssert\ServiceClient\SerializableInterface;

interface SerializedSuiteInterface extends SerializableInterface
{
    /**
     * @return non-empty-string
     */
    public function getId(): string;

    /**
     * @return non-empty-string
     */
    public function getSuiteId(): string;

    /**
     * @return array<string, string>
     */
    public function getParameters(): array;

    /**
     * @return non-empty-string
     */
    public function getState(): string;

    public function getFailureReason(): ?string;

    public function getFailureMessage(): ?string;

    /**
     * @return array{
     *   id: non-empty-string,
     *   suite_id: non-empty-string,
     *   parameters: array<string, string>,
     *   state: non-empty-string,
     *   failure_reason?: string,
     *   failure_message?: string,
     * }
     */
    public function toArray(): array;
}
