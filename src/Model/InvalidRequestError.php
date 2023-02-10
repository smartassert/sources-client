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

    /**
     * @return InvalidRequestField[]
     */
    public function getInvalidRequestFields(): array
    {
        $fields = [];
        foreach ($this->getPayload() as $name => $fieldData) {
            if ('' !== $name && is_array($fieldData)) {
                $fieldDataInspector = new ArrayInspector($fieldData);
                $field = $this->createInvalidRequestField($name, $fieldDataInspector);

                if ($field instanceof InvalidRequestField) {
                    $fields[$name] = $field;
                }
            }
        }

        return $fields;
    }

    /**
     * @param non-empty-string $name
     */
    private function createInvalidRequestField(string $name, ArrayInspector $data): ?InvalidRequestField
    {
        $value = $data->getString('value');
        $message = $data->getNonEmptyString('message');

        if (null === $value || null === $message) {
            return null;
        }

        return new InvalidRequestField($name, $value, $message);
    }
}
