<?php

namespace Cspray\DatabaseTestCase\Tests\Integration;

use Cspray\DatabaseTestCase\ConnectionAdapter;
use Cspray\DatabaseTestCase\ConnectionAdapterConfig;
use Cspray\DatabaseTestCase\PdoConnectionAdapter;
use Cspray\DatabaseTestCase\PdoDriver;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PdoConnectionAdapter::class)]
class MysqlPdoConnectionAdapterTest extends ConnectionAdapterTestCase {

    protected function getExpectedUnderlyingConnectionClassName() : string {
        return \PDO::class;
    }

    protected function executeCountSql(string $table) : int {
        $connection = self::getUnderlyingConnection();
        assert($connection instanceof PDO);
        return $connection->query('SELECT COUNT(*) AS "count" FROM ' . $table)
            ->fetch(PDO::FETCH_ASSOC)['count'];
    }

    protected static function getConnectionAdapter() : ConnectionAdapter {
        return PdoConnectionAdapter::fromConnectionConfig(
            new ConnectionAdapterConfig(
                'mysql',
                'mysql',
                3306,
                'mysql',
                'mysql'
            ),
            PdoDriver::Mysql
        );
    }
}