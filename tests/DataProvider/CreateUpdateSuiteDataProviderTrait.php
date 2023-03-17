<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\DataProvider;

use SmartAssert\SourcesClient\Model\InvalidRequestField;

trait CreateUpdateSuiteDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public function createUpdateSuiteInvalidRequestDataProvider(): array
    {
        $labelTooLong = str_repeat('.', 256);

        return [
            'label missing' => [
                'label' => '',
                'tests' => [],
                'expected' => new InvalidRequestField(
                    'label',
                    '',
                    'This value should be between 1 and 255 characters long.'
                ),
            ],
            'label too long' => [
                'label' => $labelTooLong,
                'tests' => [],
                'expected' => new InvalidRequestField(
                    'label',
                    $labelTooLong,
                    'This value should be between 1 and 255 characters long.'
                ),
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function createUpdateSuiteSuccessDataProvider(): array
    {
        return [
            'empty tests' => [
                'label' => md5((string) rand()),
                'tests' => [],
            ],
            'non-empty tests' => [
                'label' => md5((string) rand()),
                'tests' => ['test1.yaml', 'test2.yaml'],
            ],
        ];
    }
}
