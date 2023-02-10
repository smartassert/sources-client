<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class FilesystemError extends AbstractError implements ErrorInterface
{
    public function getFile(): string
    {
        return $this->getPayloadStringValue('file');
    }

    public function getMessage(): string
    {
        return $this->getPayloadStringValue('message');
    }
}
