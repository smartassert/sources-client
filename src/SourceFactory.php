<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\RunSource;
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

        if ('run' === $type) {
            return $this->createRunSource($data);
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
        $userId = $dataInspector->getNonEmptyString('user_id');
        $label = $dataInspector->getNonEmptyString('label');

        if (null === $id || null === $userId || null === $label) {
            return null;
        }

        return new FileSource($id, $userId, $label);
    }

    /**
     * @param array<mixed> $data
     */
    public function createGitSource(array $data): ?GitSource
    {
        $dataInspector = new ArrayInspector($data);

        $id = $dataInspector->getNonEmptyString('id');
        $userId = $dataInspector->getNonEmptyString('user_id');
        $label = $dataInspector->getNonEmptyString('label');
        $hostUrl = $dataInspector->getNonEmptyString('host_url');
        $path = $dataInspector->getNonEmptyString('path');
        $hasCredentials = $dataInspector->getBoolean('has_credentials');

        if (
            null === $id || null === $userId || null === $label
            || null === $hostUrl || null === $path || null === $hasCredentials
        ) {
            return null;
        }

        return new GitSource($id, $userId, $label, $hostUrl, $path, $hasCredentials);
    }

    /**
     * @param array<mixed> $data
     */
    public function createRunSource(array $data): ?RunSource
    {
        $dataInspector = new ArrayInspector($data);

        $id = $dataInspector->getNonEmptyString('id');
        $userId = $dataInspector->getNonEmptyString('user_id');
        $parameters = $dataInspector->getArray('parameters');
        $state = $dataInspector->getNonEmptyString('state');
        $parent = $dataInspector->getString('parent');
        $failureReason = $dataInspector->getString('failure_reason');
        $failureMessage = $dataInspector->getString('failure_message');

        if (null === $id || null === $userId || null === $state || '' === $parent) {
            return null;
        }

        $filteredParameters = [];
        foreach ($parameters as $key => $value) {
            if (is_string($value)) {
                $filteredParameters[$key] = $value;
            }
        }

        return new RunSource($id, $userId, $filteredParameters, $state, $parent, $failureReason, $failureMessage);
    }
}
