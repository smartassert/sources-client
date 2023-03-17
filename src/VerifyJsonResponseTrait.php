<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient;

use SmartAssert\ServiceClient\Exception\HttpResponseExceptionInterface;
use SmartAssert\ServiceClient\Exception\InvalidResponseDataException;
use SmartAssert\ServiceClient\Exception\InvalidResponseTypeException;
use SmartAssert\ServiceClient\Response\JsonResponse;
use SmartAssert\ServiceClient\Response\ResponseInterface;

trait VerifyJsonResponseTrait
{
    /**
     * @throws HttpResponseExceptionInterface
     * @throws InvalidResponseDataException
     * @throws InvalidResponseTypeException
     */
    public function verifyJsonResponse(
        ResponseInterface $response,
        ExceptionFactory $exceptionFactory,
    ): JsonResponse {
        if (!$response->isSuccessful()) {
            throw $exceptionFactory->createFromResponse($response);
        }

        if (!$response instanceof JsonResponse) {
            throw InvalidResponseTypeException::create($response, JsonResponse::class);
        }

        return $response;
    }
}
