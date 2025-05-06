<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\ConnectionAdapter;

use Cspray\DatabaseTesting\DatabaseRepresentation\Table;
use Cspray\DatabaseTesting\Fixture\Fixture;

/**
 * @api
 * @template UnderlyingConnection of object
 */
interface ConnectionAdapter {

    public function establishConnection() : void;

    public function closeConnection() : void;

    /**
     * @return UnderlyingConnection
     */
    public function underlyingConnection() : object;

    public function beginTransaction() : void;

    public function rollback() : void;

    public function truncateTable(string $table) : void;

    /**
     * @param non-empty-list<Fixture> $fixtures
     */
    public function insert(array $fixtures) : void;

    public function selectAll(string $name) : Table;

}