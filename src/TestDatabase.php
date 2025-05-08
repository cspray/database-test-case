<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting;

use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapter;
use Cspray\DatabaseTesting\DatabaseRepresentation\Table;
use Cspray\DatabaseTesting\Fixture\Fixture;
use Cspray\DatabaseTesting\Internal\ClosureDataProviderTable;

/**
 * Represents the public API testing framework extensions should interact with to establish test database connections
 * and ensure the state of the database before and after tests.
 *
 * @api
 */
final class TestDatabase {

    private function __construct(
        private readonly ConnectionAdapter $connectionAdapter,
    ) {}

    public function connectionAdapter() : ConnectionAdapter {
        return $this->connectionAdapter;
    }

    /**
     * @template UnderlyingConnection of object
     * @return UnderlyingConnection
     */
    public function connection() : object {
        return $this->connectionAdapter->underlyingConnection();
    }

    /**
     * @param list<Fixture> $fixtures
     */
    public function loadFixtures(array $fixtures) : void {
        $this->connectionAdapter->insert($fixtures);
    }

    /**
     * Allow for introspection of a database table.
     *
     * @param non-empty-string $name
     * @return Table
     */
    public function table(string $name) : Table {
        return new ClosureDataProviderTable($name, fn() => $this->connectionAdapter->selectAll($name));
    }

}