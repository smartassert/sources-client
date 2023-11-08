<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

interface SourceInterface
{
    /**
     * @return non-empty-string
     */
    public function getId(): string;

    /**
     * @return non-empty-string
     */
    public function getUserId(): string;

    /**
     * @return non-empty-string
     */
    public function getType(): string;

    /**
     * @return non-empty-string
     */
    public function getLabel(): string;

    /**
     * @return null|int<0, max>
     */
    public function getDeletedAt(): ?int;
}
