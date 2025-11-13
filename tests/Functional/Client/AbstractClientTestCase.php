<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\ServiceClient\ExceptionFactory\CurlExceptionFactory;
use SmartAssert\ServiceClient\ResponseFactory\ResponseFactory;
use SmartAssert\SourcesClient\ExceptionFactory;
use SmartAssert\SourcesClient\RequestFactory;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\CommonNonSuccessResponseDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;
use webignition\HttpHistoryContainer\Container as HttpHistoryContainer;
use webignition\HttpHistoryContainer\MiddlewareFactory;

abstract class AbstractClientTestCase extends TestCase
{
    use CommonNonSuccessResponseDataProviderTrait;
    use InvalidJsonResponseExceptionDataProviderTrait;
    use NetworkErrorExceptionDataProviderTrait;

    protected const API_KEY = 'api key value';

    protected MockHandler $mockHandler;
    protected RequestFactory $requestFactory;
    protected ServiceClient $serviceClient;
    protected ExceptionFactory $exceptionFactory;
    private HttpHistoryContainer $httpHistoryContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();

        $httpFactory = new HttpFactory();

        $handlerStack = HandlerStack::create($this->mockHandler);

        $this->httpHistoryContainer = new HttpHistoryContainer();
        $handlerStack->push(MiddlewareFactory::create($this->httpHistoryContainer));
        $httpClient = new HttpClient(['handler' => $handlerStack]);

        $this->requestFactory = new RequestFactory('https://sources.example.com');

        $responseFactory = ResponseFactory::createFactory();
        $this->serviceClient = new ServiceClient(
            $httpFactory,
            $httpFactory,
            $httpClient,
            $responseFactory,
            new CurlExceptionFactory()
        );
        $this->exceptionFactory = new ExceptionFactory();
    }

    /**
     * @param class-string<\Throwable> $expectedExceptionClass
     */
    #[DataProvider('clientActionThrowsExceptionDataProvider')]
    public function testClientActionThrowsException(
        ClientExceptionInterface|ResponseInterface $httpFixture,
        string $expectedExceptionClass,
    ): void {
        $this->mockHandler->append($httpFixture);

        $this->expectException($expectedExceptionClass);

        ($this->createClientActionCallable())();
    }

    /**
     * @return array<mixed>
     */
    abstract public static function clientActionThrowsExceptionDataProvider(): array;

    #[DataProvider('commonNonSuccessResponseDataProvider')]
    public function testClientActionThrowsNonSuccessResponseException(ResponseInterface $httpFixture): void
    {
        $this->mockHandler->append($httpFixture);

        try {
            ($this->createClientActionCallable())();

            self::fail(NonSuccessResponseException::class . ' not thrown');
        } catch (NonSuccessResponseException $e) {
            self::assertSame($httpFixture, $e->getHttpResponse());
        }
    }

    public function testClientActionRequestProperties(): void
    {
        $this->mockHandler->append($this->getClientActionSuccessResponse());

        ($this->createClientActionCallable())();

        $request = $this->getLastRequest();
        self::assertSame($this->getExpectedRequestMethod(), $request->getMethod());
        self::assertSame('Bearer ' . self::API_KEY, $request->getHeaderLine('authorization'));
    }

    protected function getLastRequest(): RequestInterface
    {
        $request = $this->httpHistoryContainer->getTransactions()->getRequests()->getLast();
        \assert($request instanceof RequestInterface);

        return $request;
    }

    abstract protected function createClientActionCallable(): callable;

    abstract protected function getExpectedRequestMethod(): string;

    abstract protected function getClientActionSuccessResponse(): ResponseInterface;
}
