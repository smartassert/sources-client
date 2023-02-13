<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\SourceInterface;

class SourceFactory
{
    /**
     * @param array<mixed> $data
     */
    public function create(array $data): ?SourceInterface
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
        $dataInspector = new ArrayInspector($data);

        $id = $dataInspector->getNonEmptyString('id');
        $label = $dataInspector->getNonEmptyString('label');

        if (null === $id || null === $label) {
            return null;
        }

        return new FileSource($id, $label);
    }

    /**
     * @param array<mixed> $data
     */
    public function createGitSource(array $data): ?GitSource
    {
        $dataInspector = new ArrayInspector($data);

        $id = $dataInspector->getNonEmptyString('id');
        $label = $dataInspector->getNonEmptyString('label');
        $hostUrl = $dataInspector->getNonEmptyString('host_url');
        $path = $dataInspector->getNonEmptyString('path');
        $hasCredentials = $dataInspector->getBoolean('has_credentials');

        if (null === $id || null === $label || null === $hostUrl || null === $path || null === $hasCredentials) {
            return null;
        }

        return new GitSource($id, $label, $hostUrl, $path, $hasCredentials);
    }
}
