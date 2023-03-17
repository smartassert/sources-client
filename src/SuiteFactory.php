<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\SourcesClient\Model\Suite;

class SuiteFactory
{
    /**
     * @param array<mixed> $data
     */
    public function create(array $data): ?Suite
    {
        $dataInspector = new ArrayInspector($data);

        $id = $dataInspector->getNonEmptyString('id');
        $sourceId = $dataInspector->getNonEmptyString('source_id');
        $label = $dataInspector->getNonEmptyString('label');
        if (null === $id || null === $sourceId || null === $label) {
            return null;
        }

        $tests = $dataInspector->getNonEmptyStringArray('tests');

        $deletedAt = $dataInspector->getInteger('deleted_at');
        if ($deletedAt < 0) {
            $deletedAt = null;
        }

        return new Suite($id, $sourceId, $label, $tests, $deletedAt);
    }
}
