<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\SourcesClient\Model\InvalidRequestField;

class InvalidRequestFieldFactory
{
    /**
     * @param non-empty-string $name
     */
    public function create(string $name, ArrayInspector $data): ?InvalidRequestField
    {
        $value = $data->getString('value');
        $message = $data->getNonEmptyString('message');

        if (null === $value || null === $message) {
            return null;
        }

        return new InvalidRequestField($name, $value, $message);
    }
}
