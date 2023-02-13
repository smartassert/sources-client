<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class FileSource extends AbstractOriginSource
{
    public function __construct(string $id, string $userId, string $label)
    {
        parent::__construct($id, $userId, 'file', $label);
    }
}
