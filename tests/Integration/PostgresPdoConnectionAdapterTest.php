<?php

namespace Cspray\DatabaseTestCase\Tests\Integration;

use Cspray\DatabaseTestCase\ConnectionAdapter;
use Cspray\DatabaseTestCase\ConnectionAdapterConfig;
use Cspray\DatabaseTestCase\PdoConnectionAdapter;
use Cspray\DatabaseTestCase\PdoDriver;
use Cspray\DatabaseTestCase\Tests\Integration\Helper\PostgresConnectionConfig;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PdoConnectionAdapter::class)]
class PostgresPdoConnectionAdapterTest extends ConnectionAdapterTestCase {

    protected static function getConnectionAdapter() : ConnectionAdapter {
        return new PdoConnectionAdapter(
            new PostgresConnectionConfig(),
            PdoDriver::Postgresql
        );
    }

    protected function getExpectedUnderlyingConnectionClassName() : string {
        return PDO::class;
    }

    protected function executeCountSql(string $table) : int {
        $connection = self::getUnderlyingConnection();
        assert($connection instanceof PDO);
        return $connection->query('SELECT COUNT(*) AS "count" FROM ' . $table)
            ->fetch(PDO::FETCH_ASSOC)['count'];
    }
}
