<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\SourcesClient\Model\SourceInterface;

class SourceClient implements SourceClientInterface
{
    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SourceFactory $sourceFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    public function list(string $token): array
    {
        try {
            $response = $this->serviceClient->sendRequest(
                $this->requestFactory->createSourcesRequest($token)
            );
        } catch (NonSuccessResponseException $e) {
            throw $this->exceptionFactory->createFromResponse($e->getResponse());
        }

        if (!$response instanceof JsonResponse) {
            throw InvalidResponseTypeException::create($response, JsonResponse::class);
        }

        $sources = [];

        foreach ($response->getData() as $sourceData) {
            if (is_array($sourceData)) {
                $source = $this->sourceFactory->create($sourceData);

                if ($source instanceof SourceInterface) {
                    $sources[] = $source;
                }
            }
        }

        return $sources;
    }
}
