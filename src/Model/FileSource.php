<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

class FileSource extends AbstractOriginSource
{
    public function __construct(string $id, string $label)
    {
        parent::__construct($id, 'file', $label);
    }
}
