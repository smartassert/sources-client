<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\SourcesClient\Model\SerializedSuite;

class SerializedSuiteFactory
{
    /**
     * @param array<mixed> $data
     */
    public function create(array $data): ?SerializedSuite
    {
        $id = $this->getNonEmptyString($data, 'id');
        $suiteId = $this->getNonEmptyString($data, 'suite_id');
        $state = $this->getNonEmptyString($data, 'state');
        $isPrepared = $this->getBoolean($data, 'is_prepared');
        $hasEndState = $this->getBoolean($data, 'has_end_state');

        if (null === $id || null === $suiteId || null === $state || null === $isPrepared || null === $hasEndState) {
            return null;
        }

        $parameters = [];
        $responseParameters = $data['parameters'] ?? [];
        $responseParameters = is_array($responseParameters) ? $responseParameters : [];

        foreach ($responseParameters as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $parameters[$key] = $value;
            }
        }

        $failureReason = $this->getString($data, 'failure_reason');
        $failureMessage = $this->getString($data, 'failure_message');

        return new SerializedSuite(
            $id,
            $suiteId,
            $parameters,
            $state,
            $isPrepared,
            $hasEndState,
            $failureReason,
            $failureMessage
        );
    }

    /**
     * @param array<mixed> $data
     */
    private function getString(array $data, string $name): ?string
    {
        $value = $data[$name] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * @param array<mixed> $data
     *
     * @return null|non-empty-string
     */
    private function getNonEmptyString(array $data, string $name): ?string
    {
        $value = $this->getString($data, $name);

        return '' === $value ? null : $value;
    }

    /**
     * @param array<mixed> $data
     */
    private function getBoolean(array $data, string $name): ?bool
    {
        $value = $data[$name] ?? null;

        return is_bool($value) ? $value : null;
    }
}
