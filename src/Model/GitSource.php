<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class GitSource extends AbstractSource
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $userId
     * @param non-empty-string $label
     * @param non-empty-string $hostUrl
     * @param non-empty-string $path
     * @param null|int<0, max> $deletedAt
     */
    public function __construct(
        string $id,
        string $userId,
        string $label,
        private readonly string $hostUrl,
        private readonly string $path,
        private readonly bool $hasCredentials,
        ?int $deletedAt,
    ) {
        parent::__construct($id, $userId, 'git', $label, $deletedAt);
    }

    /**
     * @return non-empty-string
     */
    public function getHostUrl(): string
    {
        return $this->hostUrl;
    }

    /**
     * @return non-empty-string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function hasCredentials(): bool
    {
        return $this->hasCredentials;
    }
}
