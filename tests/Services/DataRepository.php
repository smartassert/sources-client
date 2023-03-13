<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Services;

class DataRepository
{
    private static ?\PDO $connection = null;

    public function __construct(
        private readonly string $databaseDsn,
    ) {
    }

    public function removeAllData(): void
    {
        $tableNames = [
            'file_source',
            'git_source',
            'source',
        ];

        foreach ($tableNames as $tableName) {
            $this->getConnection()->query('TRUNCATE TABLE ' . $tableName . ' CASCADE');
        }
    }

    public function getConnection(): \PDO
    {
        if (null === self::$connection) {
            self::$connection = new \PDO($this->databaseDsn);
        }

        return self::$connection;
    }
}
