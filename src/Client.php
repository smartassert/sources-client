<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ArrayInspector\ArrayInspector;
use SmartAssert\ServiceClient\Authentication\BearerAuthentication;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseContentException;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\ServiceClient\Request;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\ServiceClient\Response\Response;
use SmartAssert\SourcesClient\Model\FileSource;
use SmartAssert\SourcesClient\Model\GitSource;
use SmartAssert\SourcesClient\Model\InvalidRequestError;

class Client
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly ServiceClient $serviceClient,
        private readonly InvalidRequestErrorFactory $invalidRequestErrorFactory,
    ) {
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $label
     *
     * @throws ClientExceptionInterface
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     * @throws InvalidModelDataException
     */
    public function createFileSource(string $token, string $label): FileSource|InvalidRequestError
    {
        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            (new Request('POST', $this->createUrl('/file')))
                ->withAuthentication(new BearerAuthentication($token))
                ->withPayload(new UrlEncodedPayload([
                    'label' => $label,
                ]))
        );

        if (400 === $response->getStatusCode()) {
            return $this->createInvalidRequestError($response);
        }

        if (!$response->isSuccessful()) {
            throw new NonSuccessResponseException($response->getHttpResponse());
        }

        $responseDataInspector = new ArrayInspector($response->getData());

        $label = $responseDataInspector->getNonEmptyString('label');
        $id = $responseDataInspector->getNonEmptyString('id');

        if (null === $label || null === $id) {
            throw InvalidModelDataException::fromJsonResponse(FileSource::class, $response);
        }

        return new FileSource($label, $id);
    }

    /**
     * @param non-empty-string $token
     * @param non-empty-string $hostUrl
     * @param non-empty-string $path
     * @param ?non-empty-string $credentials
     *
     * @throws ClientExceptionInterface
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     * @throws NonSuccessResponseException
     * @throws InvalidModelDataException
     */
    public function createGitSource(
        string $token,
        string $label,
        string $hostUrl,
        string $path,
        ?string $credentials,
    ): GitSource|InvalidRequestError {
        $payload = [
            'label' => $label,
            'host-url' => $hostUrl,
            'path' => $path,
        ];

        if (is_string($credentials)) {
            $payload['credentials'] = $credentials;
        }

        $response = $this->serviceClient->sendRequestForJsonEncodedData(
            (new Request('POST', $this->createUrl('/git')))
                ->withAuthentication(new BearerAuthentication($token))
                ->withPayload(new UrlEncodedPayload($payload))
        );

        if (400 === $response->getStatusCode()) {
            return $this->createInvalidRequestError($response);
        }

        if (!$response->isSuccessful()) {
            throw new NonSuccessResponseException($response->getHttpResponse());
        }

        $responseDataInspector = new ArrayInspector($response->getData());

        $label = $responseDataInspector->getNonEmptyString('label');
        $hostUrl = $responseDataInspector->getNonEmptyString('host_url');
        $path = $responseDataInspector->getNonEmptyString('path');
        $id = $responseDataInspector->getNonEmptyString('id');
        $hasCredentials = $responseDataInspector->getBoolean('has_credentials');

        if (null === $label || null === $hostUrl || null === $path || null === $id || null === $hasCredentials) {
            throw InvalidModelDataException::fromJsonResponse(GitSource::class, $response);
        }

        return new GitSource($label, $hostUrl, $path, $hasCredentials, $id);
    }

    /**
     * @param non-empty-string $path
     *
     * @return non-empty-string
     */
    private function createUrl(string $path): string
    {
        return rtrim($this->baseUrl, '/') . $path;
    }

    /**
     * @throws InvalidModelDataException
     * @throws InvalidResponseContentException
     * @throws InvalidResponseDataException
     *
     * @return InvalidRequestError
     */
    private function createInvalidRequestError(JsonResponse $response): InvalidRequestError
    {
        $invalidRequestError = $this->invalidRequestErrorFactory->create(
            $response->getHttpResponse(),
            new ArrayInspector($response->getData())
        );

        if (null === $invalidRequestError) {
            throw InvalidModelDataException::fromJsonResponse(InvalidRequestError::class, $response);
        }

        return $invalidRequestError;
    }
}
