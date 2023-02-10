<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\GitSource;

class SourceFactory
{
    /**
     * @param array<mixed> $data
     */
    public function create(array $data): FileSource|GitSource|null
    {
        $type = $data['type'] ?? null;

        if ('file' === $type) {
            return $this->createFileSource($data);
        }

        if ('git' === $type) {
            return $this->createGitSource($data);
        }

        return null;
    }

    /**
     * @param array<mixed> $data
     */
    public function createFileSource(array $data): ?FileSource
    {
        $responseDataInspector = new ArrayInspector($data);

        $label = $responseDataInspector->getNonEmptyString('label');
        $id = $responseDataInspector->getNonEmptyString('id');

        if (null === $label || null === $id) {
            return null;
        }

        return new FileSource($label, $id);
    }

    /**
     * @param array<mixed> $data
     */
    public function createGitSource(array $data): ?GitSource
    {
        $responseDataInspector = new ArrayInspector($data);

        $label = $responseDataInspector->getNonEmptyString('label');
        $hostUrl = $responseDataInspector->getNonEmptyString('host_url');
        $path = $responseDataInspector->getNonEmptyString('path');
        $id = $responseDataInspector->getNonEmptyString('id');
        $hasCredentials = $responseDataInspector->getBoolean('has_credentials');

        if (null === $label || null === $hostUrl || null === $path || null === $id || null === $hasCredentials) {
            return null;
        }

        return new GitSource($label, $hostUrl, $path, $hasCredentials, $id);
    }
}
