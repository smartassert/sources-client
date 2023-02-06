<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class GitSource extends AbstractSource
{
    /**
     * @param non-empty-string $hostUrl
     * @param non-empty-string $path
     * @param non-empty-string $id
     */
    public function __construct(
        public readonly string $hostUrl,
        public readonly string $path,
        public readonly bool $hasCredentials,
        string $id
    ) {
        parent::__construct($id);
    }
}
