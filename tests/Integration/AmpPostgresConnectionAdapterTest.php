<?php

namespace Cspray\DatabaseTestCase\Tests\Integration;

use Amp\Postgres\PostgresLink;
use Cspray\DatabaseTestCase\AmpPostgresConnectionAdapter;
use Cspray\DatabaseTestCase\ConnectionAdapter;
use Cspray\DatabaseTestCase\Tests\Integration\Helper\PostgresConnectionConfig;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AmpPostgresConnectionAdapter::class)]
class AmpPostgresConnectionAdapterTest extends ConnectionAdapterTestCase {

    protected function getExpectedUnderlyingConnectionClassName() : string {
        return PostgresLink::class;
    }

    protected function executeCountSql(string $table) : int {
        $connection = self::getUnderlyingConnection();
        assert($connection instanceof PostgresLink);
        return $connection->query('SELECT COUNT(*) AS count FROM ' . $table)->fetchRow()['count'];
    }

    protected static function getConnectionAdapter() : ConnectionAdapter {
        return AmpPostgresConnectionAdapter::fromConnectionConfig(new PostgresConnectionConfig());
    }
}