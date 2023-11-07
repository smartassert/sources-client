<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Request;

class GitSourceRequest implements RequestInterface
{
    /**
     * @param 'POST'|'PUT'          $method
     * @param non-empty-string      $label
     * @param null|non-empty-string $resourceId
     */
    public function __construct(
        private readonly string $method,
        private readonly string $label,
        private readonly string $hostUrl,
        private readonly string $path,
        private readonly ?string $credentials,
        private readonly ?string $resourceId = null,
    ) {
    }

    /**
     * @return null|non-empty-string
     */
    public function getResourceId(): ?string
    {
        return $this->resourceId;
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

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRoute(): string
    {
        return 'git_source';
    }
}
