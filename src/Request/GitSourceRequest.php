<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class GitSourceRequest implements SourceRequestInterface
{
    /**
     * @param non-empty-string      $label
     * @param null|non-empty-string $sourceId
     */
    public function __construct(
        private readonly string $label,
        private readonly string $hostUrl,
        private readonly string $path,
        private readonly ?string $credentials,
        private readonly ?string $sourceId = null,
    ) {
    }

    public function hasId(): bool
    {
        return is_string($this->sourceId);
    }

    /**
     * @return null|non-empty-string
     */
    public function getId(): ?string
    {
        return $this->sourceId;
    }

    public function getPayload(): array
    {
        $payload = [
            'type' => 'git',
            'label' => $this->label,
            'host-url' => $this->hostUrl,
            'path' => $this->path,
        ];

        if (is_string($this->credentials)) {
            $payload['credentials'] = $this->credentials;
        }

        return $payload;
    }
}
