<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\SourcesClient\Model\SourceInterface;

class SourceClient implements SourceClientInterface
{
    use VerifyJsonResponseTrait;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly ServiceClient $serviceClient,
        private readonly SourceFactory $sourceFactory,
        private readonly ExceptionFactory $exceptionFactory,
    ) {
    }

    public function list(string $token): array
    {
        $response = $this->serviceClient->sendRequest(
            $this->requestFactory->createSourcesRequest($token)
        );

        $response = $this->verifyJsonResponse($response, $this->exceptionFactory);

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
