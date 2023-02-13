<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class GitSource extends AbstractOriginSource
{
    /**
     * @param non-empty-string $label
     * @param non-empty-string $hostUrl
     * @param non-empty-string $path
     * @param non-empty-string $id
     */
    public function __construct(
        string $id,
        string $label,
        private readonly string $hostUrl,
        private readonly string $path,
        private readonly bool $hasCredentials,
    ) {
        parent::__construct($id, 'git', $label);
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
