<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Message\ResponseInterface;
use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\SourcesClient\Model\InvalidRequestError;
use SmartAssert\SourcesClient\Model\InvalidRequestField;

class InvalidRequestErrorFactory
{
    // {"error":{"type":"invalid_request","payload":{"label":{"value":"","message":"This value should not be blank."}}}}

    public function __construct(
        private readonly InvalidRequestFieldFactory $fieldFactory,
    ) {
    }

    public function create(ResponseInterface $httpResponse, ArrayInspector $data): ?InvalidRequestError
    {
        if (!$data->has('error', 'array')) {
            return null;
        }

        $errorDataInspector = new ArrayInspector($data->getArray('error'));

        $type = $errorDataInspector->getNonEmptyString('type');
        if ('invalid_request' !== $type) {
            return null;
        }

        $payload = $errorDataInspector->getArray('payload');
        if ([] === $payload) {
            return null;
        }

        $fields = [];
        foreach ($payload as $name => $fieldData) {
            if ('' !== $name && is_array($fieldData)) {
                $fieldDataInspector = new ArrayInspector($fieldData);
                $field = $this->fieldFactory->create($name, $fieldDataInspector);

                if ($field instanceof InvalidRequestField) {
                    $fields[$name] = $field;
                }
            }
        }

        return new InvalidRequestError($httpResponse, $fields);
    }
}
