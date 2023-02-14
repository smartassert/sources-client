<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

use Psr\Http\Message\ResponseInterface;
use SmartAssert\ArrayInspector\ArrayInspector;

class InvalidRequestError extends AbstractError implements ErrorInterface
{
    /**
     * @param array<mixed> $payload
     */
    public function __construct(ResponseInterface $httpResponse, array $payload)
    {
        parent::__construct($httpResponse, 'invalid_request', $payload);
    }

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
