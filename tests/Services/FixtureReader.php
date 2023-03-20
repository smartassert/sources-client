<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Services;

class FixtureReader
{
    public function __construct(
        private readonly string $fixturePath,
    ) {
    }

    public function read(string $path): string
    {
        return (string) file_get_contents($this->fixturePath . $path);
    }
}
