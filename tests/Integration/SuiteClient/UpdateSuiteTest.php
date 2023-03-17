<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Integration\SuiteClient;

use SmartAssert\SourcesClient\Exception\InvalidRequestException;
use SmartAssert\SourcesClient\Exception\ModifyReadOnlyEntityException;
use SmartAssert\SourcesClient\Model\InvalidRequestField;
use SmartAssert\SourcesClient\Model\SourceInterface;
use SmartAssert\SourcesClient\Model\Suite;
use SmartAssert\SourcesClient\Tests\DataProvider\CreateUpdateSuiteDataProviderTrait;
use SmartAssert\SourcesClient\Tests\Integration\AbstractIntegrationTestCase;

class UpdateSuiteTest extends AbstractIntegrationTestCase
{
    use CreateUpdateSuiteDataProviderTrait;

    private SourceInterface $source;
    private Suite $suite;

    protected function setUp(): void
    {
        parent::setUp();

        $this->source = self::$sourceClient->createFileSource(self::$user1ApiToken->token, md5((string) rand()));
        $this->suite = self::$suiteClient->create(
            self::$user1ApiToken->token,
            $this->source->getId(),
            md5((string) rand()),
            []
        );
    }

    /**
     * @dataProvider createUpdateSuiteInvalidRequestDataProvider
     *
     * @param non-empty-string   $label
     * @param non-empty-string[] $tests
     */
    public function testUpdateInvalidRequest(string $label, array $tests, InvalidRequestField $expected): void
    {
        try {
            self::$suiteClient->update(
                self::$user1ApiToken->token,
                $this->suite->getId(),
                $this->source->getId(),
                $label,
                $tests,
            );
        } catch (\Throwable $e) {
            self::assertInstanceOf(InvalidRequestException::class, $e);
            self::assertEquals($expected, $e->getInvalidRequestField());
        }
    }

    /**
     * @dataProvider createUpdateSuiteSuccessDataProvider
     *
     * @param non-empty-string   $label
     * @param non-empty-string[] $tests
     */
    public function testUpdateSuccess(string $label, array $tests): void
    {
        $newSource = self::$sourceClient->createFileSource(self::$user1ApiToken->token, md5((string) rand()));

        $suite = self::$suiteClient->update(
            self::$user1ApiToken->token,
            $this->suite->getId(),
            $newSource->getId(),
            $label,
            $tests,
        );

        self::assertSame($this->suite->getId(), $suite->getId());
        self::assertSame($newSource->getId(), $suite->getSourceId());
        self::assertSame($label, $suite->getLabel());
        self::assertSame($tests, $suite->getTests());
        self::assertNull($suite->getDeletedAt());
    }

    public function testUpdateDeletedSuite(): void
    {
        self::$suiteClient->delete(self::$user1ApiToken->token, $this->suite->getId());

        self::expectException(ModifyReadOnlyEntityException::class);
        self::expectErrorMessage('Cannot modify read-only suite ' . $this->suite->getId() . '.');

        self::$suiteClient->update(
            self::$user1ApiToken->token,
            $this->suite->getId(),
            $this->source->getId(),
            md5((string) rand()),
            [],
        );
    }
}
