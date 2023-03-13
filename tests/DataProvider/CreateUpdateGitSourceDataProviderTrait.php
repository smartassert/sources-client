<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\DataProvider;

use SmartAssert\SourcesClient\Model\InvalidRequestField;

trait CreateUpdateGitSourceDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public function createUpdateGitSourceInvalidRequestDataProvider(): array
    {
        $label = 'git source label';
        $labelTooLong = str_repeat('.', 256);
        $hostUrl = 'https://example.com/repository.git';
        $hostUrlTooLong = str_repeat('.', 256);
        $path = '/';
        $pathTooLong = str_repeat('.', 256);

        return [
            'label missing' => [
                'label' => '  ',
                'hostUrl' => $hostUrl,
                'path' => $path,
                'expected' => new InvalidRequestField(
                    'label',
                    '',
                    'This value should be between 1 and 255 characters long.'
                ),
            ],
            'label too long' => [
                'label' => $labelTooLong,
                'hostUrl' => $hostUrl,
                'path' => $path,
                'expected' => new InvalidRequestField(
                    'label',
                    $labelTooLong,
                    'This value should be between 1 and 255 characters long.'
                ),
            ],
            'host url missing' => [
                'label' => $label,
                'hostUrl' => '   ',
                'path' => $path,
                'expected' => new InvalidRequestField(
                    'host-url',
                    '',
                    'This value should be between 1 and 255 characters long.'
                ),
            ],
            'host url too long' => [
                'label' => $label,
                'hostUrl' => $hostUrlTooLong,
                'path' => $path,
                'expected' => new InvalidRequestField(
                    'host-url',
                    $hostUrlTooLong,
                    'This value should be between 1 and 255 characters long.'
                ),
            ],
            'path missing' => [
                'label' => $label,
                'hostUrl' => $hostUrl,
                'path' => '  ',
                'expected' => new InvalidRequestField(
                    'path',
                    '',
                    'This value should be between 1 and 255 characters long.'
                ),
            ],
            'path too long' => [
                'label' => $label,
                'hostUrl' => $hostUrl,
                'path' => $pathTooLong,
                'expected' => new InvalidRequestField(
                    'path',
                    $pathTooLong,
                    'This value should be between 1 and 255 characters long.'
                ),
            ],
        ];
    }
}
