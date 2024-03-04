<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\ExceptionFactory\CurlExceptionFactory;
use SmartAssert\ServiceClient\ResponseFactory\ResponseFactory;
use SmartAssert\SourcesClient\ExceptionFactory;
use SmartAssert\SourcesClient\FileClient;
use SmartAssert\SourcesClient\FileSourceClient;
use SmartAssert\SourcesClient\GitSourceClient;
use SmartAssert\SourcesClient\RequestFactory;
use SmartAssert\SourcesClient\SerializedSuiteClient;
use SmartAssert\SourcesClient\SerializedSuiteFactory;
use SmartAssert\SourcesClient\SourceFactory;
use SmartAssert\SourcesClient\SuiteClient;
use SmartAssert\SourcesClient\SuiteFactory;
use SmartAssert\SourcesClient\Tests\Services\DataRepository;
use SmartAssert\SourcesClient\Tests\Services\FixtureReader;
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
    protected static FileClient $fileClient;
    protected static FileSourceClient $fileSourceClient;
    protected static GitSourceClient $gitSourceClient;
    protected static SuiteClient $suiteClient;
    protected static SerializedSuiteClient $serializedSuiteClient;
    protected static Token $user1ApiToken;
    protected static DataRepository $dataRepository;
    protected static RequestFactory $requestFactory;
    protected static ServiceClient $serviceClient;
    protected static ExceptionFactory $exceptionFactory;
    protected static FixtureReader $fixtureReader;

    public static function setUpBeforeClass(): void
    {
        self::$requestFactory = new RequestFactory('http://localhost:9081');
        self::$serviceClient = self::createServiceClient();
        self::$exceptionFactory = new ExceptionFactory();

        self::$fileClient = new FileClient(
            self::$serviceClient,
            self::$exceptionFactory,
            'http://localhost:9081'
        );
        self::$fileSourceClient = new FileSourceClient(
            self::$serviceClient,
            new SourceFactory(),
            self::$exceptionFactory,
            'http://localhost:9081'
        );
        self::$gitSourceClient = new GitSourceClient(
            self::$serviceClient,
            new SourceFactory(),
            self::$exceptionFactory,
            'http://localhost:9081'
        );
        self::$suiteClient = new SuiteClient(
            self::$serviceClient,
            new SuiteFactory(),
            self::$exceptionFactory,
            'http://localhost:9081'
        );
        self::$serializedSuiteClient = new SerializedSuiteClient(
            self::$requestFactory,
            self::$serviceClient,
            new SerializedSuiteFactory(),
            self::$exceptionFactory
        );

        self::$user1ApiToken = self::createUserApiToken(self::USER1_EMAIL, self::USER1_PASSWORD);
        self::$dataRepository = new DataRepository(
            'pgsql:host=localhost;port=5432;dbname=sources;user=postgres;password=password!'
        );
        self::$fixtureReader = new FixtureReader(__DIR__ . '/../Fixtures/');
    }

    protected static function createUserApiToken(string $email, string $password): Token
    {
        $usersClient = new UsersClient('http://localhost:9080', self::createServiceClient());

        $frontendToken = $usersClient->createFrontendToken($email, $password);
        \assert($frontendToken instanceof Token);

        $frontendTokenUser = $usersClient->verifyFrontendToken($frontendToken->token);
        \assert($frontendTokenUser instanceof User);

        $apiKeys = $usersClient->listUserApiKeys($frontendToken->token);
        $defaultApiKey = $apiKeys->getDefault();
        \assert($defaultApiKey instanceof ApiKey);

        $apiToken = $usersClient->createApiToken($defaultApiKey->key);
        \assert($apiToken instanceof Token);

        $apiTokenUser = $usersClient->verifyApiToken($apiToken->token);
        \assert($apiTokenUser instanceof User);

        return $apiToken;
    }

    private static function createServiceClient(): ServiceClient
    {
        $httpFactory = new HttpFactory();

        return new ServiceClient(
            $httpFactory,
            $httpFactory,
            new HttpClient(),
            ResponseFactory::createFactory(),
            new CurlExceptionFactory()
        );
    }
}
