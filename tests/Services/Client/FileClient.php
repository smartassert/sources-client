<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Services\Client;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\CurlExceptionInterface;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Exception\UnauthorizedException;
use SmartAssert\ServiceClient\Payload\Payload;
use SmartAssert\ServiceClient\Request;

readonly class FileClient
{
    public function __construct(
        private ServiceClient $serviceClient,
        private string $baseUrl,
    ) {
    }

    /**
     * @throws ClientExceptionInterface
     * @throws CurlExceptionInterface
     * @throws NetworkExceptionInterface
     * @throws NonSuccessResponseException
     * @throws RequestExceptionInterface
     * @throws UnauthorizedException
     */
    public function add(string $token, string $fileSourceId, string $filename, string $content): void
    {
        $request = (
            new Request(
                'POST',
                $this->baseUrl . '/file-source/' . $fileSourceId . '/' . $filename
            )
        )
            ->withPayload(new Payload('text/x-yaml', $content))
            ->withAuthentication(new BearerAuthentication($token))
        ;

        $this->serviceClient->sendRequest($request);
    }
}
