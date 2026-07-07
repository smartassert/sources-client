<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\SourcesClient\Model\MetaState;
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

        if (null === $id || null === $suiteId || null === $state) {
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

        $metaStateData = $data['meta_state'] ?? [];
        $metaStateData = is_array($metaStateData) ? $metaStateData : [];

        $metaStateEnded = $metaStateData['ended'] ?? false;
        $metaStateEnded = is_bool($metaStateEnded) ? $metaStateEnded : false;

        $metaStateSucceeded = $metaStateData['succeeded'] ?? false;
        $metaStateSucceeded = is_bool($metaStateSucceeded) ? $metaStateSucceeded : false;

        $metaStatePending = $metaStateData['pending'] ?? true;
        $metaStatePending = is_bool($metaStatePending) ? $metaStatePending : true;

        return new SerializedSuite(
            $id,
            $suiteId,
            $parameters,
            $state,
            new MetaState(
                $metaStateEnded,
                $metaStateSucceeded,
                $metaStatePending,
            ),
            $failureReason,
            $failureMessage,
            $this->getStringCollection($data, 'previous_states'),
            $this->getStringCollection($data, 'next_states'),
        );
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<non-empty-string>
     */
    private function getStringCollection(array $data, string $name): array
    {
        $value = $data[$name] ?? [];
        $value = is_array($value) ? $value : [];

        return array_filter(
            $value,
            function (mixed $item) {
                return is_string($item) && '' !== $item;
            }
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
}
