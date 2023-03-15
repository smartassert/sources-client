<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Exception;

use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\SourcesClient\Model\InvalidRequestField;

class InvalidRequestException extends ResponseException implements HttpResponseExceptionInterface
{
    public function getInvalidRequestField(): ?InvalidRequestField
    {
        $inspector = new ArrayInspector($this->getPayload());

        $name = $inspector->getNonEmptyString('name');
        $value = $inspector->getString('value');
        $message = $inspector->getNonEmptyString('message');

        if (null === $name || null === $value || null === $message) {
            return null;
        }

        return new InvalidRequestField($name, $value, $message);
    }
}
