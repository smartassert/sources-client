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
use SmartAssert\SourcesClient\RequestFactory;
use SmartAssert\SourcesClient\SerializedSuiteClient;
use SmartAssert\SourcesClient\SerializedSuiteFactory;
use SmartAssert\SourcesClient\Tests\Services\Client\FileClient;
use SmartAssert\SourcesClient\Tests\Services\Client\FileSourceClient;
use SmartAssert\SourcesClient\Tests\Services\Client\GitSourceClient;
use SmartAssert\SourcesClient\Tests\Services\Client\SuiteClient;
use SmartAssert\SourcesClient\Tests\Services\DataRepository;
use SmartAssert\SourcesClient\Tests\Services\FixtureReader;
use SmartAssert\TestAuthenticationProviderBundle\ApiKeyProvider;
use SmartAssert\TestAuthenticationProviderBundle\ApiTokenProvider;
use SmartAssert\TestAuthenticationProviderBundle\FrontendTokenProvider;

abstract class AbstractIntegrationTestCase extends TestCase
{
    protected const USER1_EMAIL = 'user1@example.com';
    protected const USER1_PASSWORD = 'password';
    protected const USER2_EMAIL = 'user2@example.com';
    protected const USER2_PASSWORD = 'password';
    protected static FileClient $fileClient;
    protected static FileSourceClient $fileSourceClient;
    protected static GitSourceClient $gitSourceClient;
    protected static SuiteClient $suiteClient;
    protected static SerializedSuiteClient $serializedSuiteClient;

    /**
     * @var non-empty-string
     */
    protected static string $user1ApiToken;
    protected static DataRepository $dataRepository;
    protected static RequestFactory $requestFactory;
    protected static ServiceClient $serviceClient;
    protected static ExceptionFactory $exceptionFactory;
    protected static FixtureReader $fixtureReader;

    public static function setUpBeforeClass(): void
    {
        $baseUrl = 'http://localhost:9081';

        self::$requestFactory = new RequestFactory($baseUrl);
        self::$serviceClient = self::createServiceClient();
        self::$exceptionFactory = new ExceptionFactory();

        self::$fileClient = new FileClient(self::$serviceClient, $baseUrl);
        self::$fileSourceClient = new FileSourceClient(self::$serviceClient, $baseUrl);
        self::$gitSourceClient = new GitSourceClient(self::$serviceClient, $baseUrl);
        self::$suiteClient = new SuiteClient(self::$serviceClient, $baseUrl);
        self::$serializedSuiteClient = new SerializedSuiteClient(
            self::$requestFactory,
            self::$serviceClient,
            new SerializedSuiteFactory(),
            self::$exceptionFactory
        );

        self::$user1ApiToken = self::createUserApiToken(self::USER1_EMAIL);
        self::$dataRepository = new DataRepository(
            'pgsql:host=localhost;port=5432;dbname=sources;user=postgres;password=password!'
        );
        self::$fixtureReader = new FixtureReader(__DIR__ . '/../Fixtures/');
    }

    /**
     * @param non-empty-string $email
     *
     * @return non-empty-string
     */
    protected static function createUserApiToken(string $email): string
    {
        $usersBaseUrl = 'http://localhost:9080';
        $httpClient = new HttpClient();

        $frontendTokenProvider = new FrontendTokenProvider(
            [
                self::USER1_EMAIL => self::USER1_PASSWORD,
                self::USER2_EMAIL => self::USER2_PASSWORD,
            ],
            $usersBaseUrl,
            $httpClient
        );
        $apiKeyProvider = new ApiKeyProvider($usersBaseUrl, $httpClient, $frontendTokenProvider);
        $apiTokenProvider = new ApiTokenProvider($usersBaseUrl, $httpClient, $apiKeyProvider);

        return $apiTokenProvider->get($email);
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
