<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Model;

interface OriginSourceInterface extends SourceInterface
{
    public function getLabel(): string;
}
