<?php

namespace Cspray\DatabaseTestCase\Tests\Integration;

use Cspray\DatabaseTestCase\ConnectionAdapter;
use Cspray\DatabaseTestCase\ConnectionAdapterConfig;
use Cspray\DatabaseTestCase\PdoConnectionAdapter;
use Cspray\DatabaseTestCase\PdoDriver;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PdoConnectionAdapter::class)]
class PostgresPdoConnectionAdapterTest extends ConnectionAdapterTestCase {

    protected static function getConnectionAdapter() : ConnectionAdapter {
        return new PdoConnectionAdapter(
            new ConnectionAdapterConfig(
                'postgres',
                'postgres',
                5432,
                'postgres',
                'postgres'
            ),
            PdoDriver::Postgresql
        );
    }

    protected function getExpectedUnderlyingConnectionClassName() : string {
        return PDO::class;
    }

    protected function executeCountSql(string $table) : int {
        return self::getUnderlyingConnection()->query('SELECT COUNT(*) AS "count" FROM my_table')
            ->fetch(PDO::FETCH_ASSOC)['count'];
    }
}
