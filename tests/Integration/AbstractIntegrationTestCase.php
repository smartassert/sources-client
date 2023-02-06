<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\SourcesClient\Client;
use SmartAssert\SourcesClient\InvalidRequestErrorFactory;
use SmartAssert\SourcesClient\InvalidRequestFieldFactory;
use SmartAssert\UsersClient\Client as UsersClient;
use SmartAssert\UsersClient\Model\ApiKey;
use SmartAssert\UsersClient\Model\Token;
use SmartAssert\UsersClient\Model\User;

abstract class AbstractIntegrationTestCase extends TestCase
{
    protected const USER1_EMAIL = 'user1@example.com';
    protected const USER1_PASSWORD = 'password';
    protected const USER2_EMAIL = 'user1@example.com';
    protected const USER2_PASSWORD = 'password';

    protected static Client $client;
    protected static Token $user1ApiToken;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client(
            'http://localhost:9081',
            self::createServiceClient(),
            new InvalidRequestErrorFactory(
                new InvalidRequestFieldFactory(),
            ),
        );
        self::$user1ApiToken = self::createUserApiToken(self::USER1_EMAIL, self::USER1_PASSWORD);
    }

    protected static function createUserApiToken(string $email, string $password): Token
    {
        $usersClient = new UsersClient('http://localhost:9080', self::createServiceClient());

        $frontendToken = $usersClient->createFrontendToken($email, $password);
        \assert($frontendToken instanceof Token);

        $frontendTokenUser = $usersClient->verifyFrontendToken($frontendToken);
        \assert($frontendTokenUser instanceof User);

        $apiKeys = $usersClient->listUserApiKeys($frontendToken);
        $defaultApiKey = $apiKeys->getDefault();
        \assert($defaultApiKey instanceof ApiKey);

        $apiToken = $usersClient->createApiToken($defaultApiKey->key);
        \assert($apiToken instanceof Token);

        $apiTokenUser = $usersClient->verifyApiToken($apiToken);
        \assert($apiTokenUser instanceof User);

        return $apiToken;
    }

    private static function createServiceClient(): ServiceClient
    {
        $httpFactory = new HttpFactory();

        return new ServiceClient($httpFactory, $httpFactory, new HttpClient());
    }
}
