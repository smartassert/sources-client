<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\DataProvider;

use SmartAssert\SourcesClient\Model\InvalidRequestField;

trait CreateUpdateFileSourceDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public static function createUpdateFileSourceInvalidRequestDataProvider(): array
    {
        $labelTooLong = str_repeat('.', 256);

        return [
            'label missing' => [
                'label' => '  ',
                'expected' => new InvalidRequestField(
                    'label',
                    '',
                    'This value should be between 1 and 255 characters long.'
                ),
            ],
            'label too long' => [
                'label' => $labelTooLong,
                'expected' => new InvalidRequestField(
                    'label',
                    $labelTooLong,
                    'This value should be between 1 and 255 characters long.',
                ),
            ],
        ];
    }
}
