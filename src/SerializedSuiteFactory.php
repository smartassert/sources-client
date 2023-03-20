<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\SourcesClient\Model\SerializedSuite;

class SerializedSuiteFactory
{
    /**
     * @param array<mixed> $data
     */
    public function create(array $data): ?SerializedSuite
    {
        $dataInspector = new ArrayInspector($data);

        $id = $dataInspector->getNonEmptyString('id');
        $suiteId = $dataInspector->getNonEmptyString('suite_id');
        $state = $dataInspector->getNonEmptyString('state');
        if (null === $id || null === $suiteId || null === $state) {
            return null;
        }

        $parameters = [];
        $responseParameters = $dataInspector->getArray('parameters');
        foreach ($responseParameters as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $parameters[$key] = $value;
            }
        }

        $failureReason = $dataInspector->getString('failure_reason');
        $failureMessage = $dataInspector->getString('failure_message');

        return new SerializedSuite($id, $suiteId, $parameters, $state, $failureReason, $failureMessage);
    }
}
