<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Exception;

class ModifyReadOnlyEntityException extends \Exception
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
    ) {
        parent::__construct(sprintf(
            'Cannot modify read-only %s %s.',
            $this->type,
            $this->id
        ));
    }
}
