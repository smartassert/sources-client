<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Exception;

use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\SourcesClient\Model\InvalidRequestField;

class InvalidRequestException extends ResponseException implements HttpResponseExceptionInterface
{
    public function getInvalidRequestField(): ?InvalidRequestField
    {
        $name = $this->getPayload()['name'] ?? null;
        $name = is_string($name) ? $name : null;
        $name = '' === $name ? null : $name;

        $value = $this->getPayload()['value'] ?? null;
        $value = is_string($value) ? $value : null;

        $message = $this->getPayload()['message'] ?? null;
        $message = is_string($message) ? $message : null;
        $message = '' === $message ? null : $message;

        if (null === $name || null === $value || null === $message) {
            return null;
        }

        return new InvalidRequestField($name, $value, $message);
    }
}
