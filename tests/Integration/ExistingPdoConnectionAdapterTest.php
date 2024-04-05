<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase\Tests\Integration;

use Cspray\DatabaseTestCase\ConnectionAdapter;
use Cspray\DatabaseTestCase\PdoConnectionAdapter;
use Cspray\DatabaseTestCase\PdoDriver;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PdoConnectionAdapter::class)]
class ExistingPdoConnectionAdapterTest extends ConnectionAdapterTestCase {

    protected function getExpectedUnderlyingConnectionClassName() : string {
        return PDO::class;
    }

    protected function executeCountSql(string $table) : int {
        $connection = self::getUnderlyingConnection();
        assert($connection instanceof PDO);
        return $connection->query('SELECT COUNT(*) AS "count" FROM ' . $table)
            ->fetch(PDO::FETCH_ASSOC)['count'];
    }

    protected static function getConnectionAdapter() : ConnectionAdapter {
        $pdo = new PDO('sqlite::memory:');
        $pdo->query(file_get_contents(dirname(__DIR__, 2) . '/resources/schemas/sqlite.sql'));
        return PdoConnectionAdapter::fromExistingConnection($pdo, PdoDriver::Sqlite);
    }
}