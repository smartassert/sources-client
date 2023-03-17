<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use Psr\Http\Client\ClientExceptionInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidModelDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;
use SmartAssert\ServiceClient\Payload\UrlEncodedPayload;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\SourcesClient\Model\Suite;

class SuiteClient
{
    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SuiteFactory $suiteFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @param non-empty-string   $token
     * @param non-empty-string   $sourceId
     * @param non-empty-string   $label
     * @param non-empty-string[] $tests
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    public function create(string $token, string $sourceId, string $label, array $tests): Suite
    {
        return $this->makeMutationRequest($token, $sourceId, $label, $tests, null);
    }

    /**
     * @param non-empty-string      $token
     * @param non-empty-string      $sourceId
     * @param non-empty-string      $label
     * @param non-empty-string[]    $tests
     * @param null|non-empty-string $suiteId
     *
     * @throws ClientExceptionInterface
     * @throws HttpResponseExceptionInterface
     * @throws InvalidModelDataException
     */
    private function makeMutationRequest(
        string $token,
        string $sourceId,
        string $label,
        array $tests,
        ?string $suiteId,
    ): Suite {
        $payload = [
            'source_id' => $sourceId,
            'label' => $label,
            'tests' => $tests,
        ];

        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSuiteRequest(is_string($suiteId) ? 'PUT' : 'POST', $token, $suiteId)
                ->withPayload(new UrlEncodedPayload($payload))
        );

        if (!$response->isSuccessful()) {
            throw $this->exceptionFactory->createFromResponse($response);
        }

        if (!$response instanceof JsonResponse) {
            throw InvalidResponseTypeException::create($response, JsonResponse::class);
        }

        $suite = $this->suiteFactory->create($response->getData());
        if (null === $suite) {
            throw InvalidModelDataException::fromJsonResponse(Suite::class, $response);
        }

        return $suite;
    }
}
