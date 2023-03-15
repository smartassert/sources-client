<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Functional\Client;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SmartAssert\ServiceClient\Client as ServiceClient;
use SmartAssert\ServiceClient\Exception\NonSuccessResponseException;
use SmartAssert\SourcesClient\Client;
use SmartAssert\SourcesClient\ExceptionFactory;
use SmartAssert\SourcesClient\RequestFactory;
use SmartAssert\SourcesClient\RequestHandler\FileRequestHandler;
use SmartAssert\SourcesClient\RequestHandler\SourceAccessHandler;
use SmartAssert\SourcesClient\RequestHandler\SourceMutationHandler;
use SmartAssert\SourcesClient\SourceFactory;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\CommonNonSuccessResponseDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\InvalidJsonResponseExceptionDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Functional\DataProvider\NetworkErrorExceptionDataProviderTrait;
use SmartAssert\SourcesClient\UrlFactory;
use webignition\HttpHistoryContainer\Container as HttpHistoryContainer;

abstract class AbstractClientTestCase extends TestCase
{
    use CommonNonSuccessResponseDataProviderTrait;
    use InvalidJsonResponseExceptionDataProviderTrait;
    use NetworkErrorExceptionDataProviderTrait;

    protected MockHandler $mockHandler;
    protected Client $client;
    private HttpHistoryContainer $httpHistoryContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();

        $httpFactory = new HttpFactory();

        $handlerStack = HandlerStack::create($this->mockHandler);

        $this->httpHistoryContainer = new HttpHistoryContainer();
        $handlerStack->push(Middleware::history($this->httpHistoryContainer));

        $requestFactory = new RequestFactory(UrlFactory::createUrlFactory('https://sources.example.com'));
        $serviceClient = new ServiceClient($httpFactory, $httpFactory, new HttpClient(['handler' => $handlerStack]));
        $exceptionFactory = new ExceptionFactory();
        $sourceFactory = new SourceFactory();

        $this->client = new Client(
            new FileRequestHandler($requestFactory, $serviceClient, $exceptionFactory),
            new SourceMutationHandler($requestFactory, $serviceClient, $sourceFactory, $exceptionFactory),
            new SourceAccessHandler($requestFactory, $serviceClient, $sourceFactory, $exceptionFactory),
        );
    }

    /**
     * @dataProvider clientActionThrowsExceptionDataProvider
     *
     * @param class-string<\Throwable> $expectedExceptionClass
     */
    public function testClientActionThrowsException(
        ResponseInterface|ClientExceptionInterface $httpFixture,
        string $expectedExceptionClass,
    ): void {
        $this->mockHandler->append($httpFixture);

        $this->expectException($expectedExceptionClass);

        ($this->createClientActionCallable())();
    }

    /**
     * @return array<mixed>
     */
    abstract public function clientActionThrowsExceptionDataProvider(): array;

    /**
     * @dataProvider commonNonSuccessResponseDataProvider
     */
    public function testClientActionThrowsNonSuccessResponseException(ResponseInterface $httpFixture): void
    {
        $this->mockHandler->append($httpFixture);

        try {
            ($this->createClientActionCallable())();

            self::fail(NonSuccessResponseException::class . ' not thrown');
        } catch (NonSuccessResponseException $e) {
            self::assertSame($httpFixture, $e->response);
        }
    }

    protected function getLastRequest(): RequestInterface
    {
        $request = $this->httpHistoryContainer->getTransactions()->getRequests()->getLast();
        \assert($request instanceof RequestInterface);

        return $request;
    }

    abstract protected function createClientActionCallable(): callable;
}
