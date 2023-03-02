<?php

namespace Cspray\DatabaseTestCase;

use Cspray\DatabaseTestCase\DatabaseRepresentation\Row;
use Cspray\DatabaseTestCase\DatabaseRepresentation\Table;
use Cspray\DatabaseTestCase\Exception\UnableToGetTable;
use Cspray\DatabaseTestCase\Exception\MissingRequiredExtension;
use PDO;
use PDOException;

if (! extension_loaded('pdo')) {
    throw new MissingRequiredExtension('You must enable ext-pdo to use the ' . PdoConnectionAdapter::class);
}

final class PdoConnectionAdapter implements ConnectionAdapter {

    private ?PDO $connection = null;

    public function __construct(
        private readonly ConnectionAdapterConfig $adapterConfig,
        private readonly PdoDriver $pdoDriver
    ) {}

    public function establishConnection() : void {
        $this->connection = new PDO(
            sprintf(
                '%s:host=%s;port=%d;dbname=%s;user=%s;password=%s',
                $this->pdoDriver->getDsnIdentifier(),
                $this->adapterConfig->host,
                $this->adapterConfig->port,
                $this->adapterConfig->database,
                $this->adapterConfig->user,
                $this->adapterConfig->password
            )
        );
    }

    public function onTestStart() : void {
        $this->connection->query('START TRANSACTION');
    }

    public function onTestStop() : void {
        $this->connection->query('ROLLBACK');
    }

    public function closeConnection() : void {
        unset($this->connection);
        $this->connection = null;
    }

    public function loadFixture(Fixture $fixture, Fixture ...$additionalFixture) : void {
        /** @var Fixture $f */
        foreach ([$fixture, ...$additionalFixture] as $f) {
            foreach ($f->getFixtureRecords() as $fixtureRecord) {
                $statement = $this->connection->prepare($this->generateInsertSqlForParameters($fixtureRecord));
                foreach ($fixtureRecord->parameters as $col => $val) {
                    $statement->bindValue($col, $val);
                }
                $statement->execute();
            }
        }
    }

    public function getUnderlyingConnection() : PDO {
        return $this->connection;
    }

    public function getTable(string $name) : Table {
        try {
            $query = sprintf('SELECT * FROM %s', $name);
            $result = $this->connection->query($query)->fetchAll(PDO::FETCH_ASSOC);
            $table = Table::forName($name);
            foreach ($result as $row) {
                $r = null;
                foreach ($row as $col => $val) {
                    $r = $r === null ? Row::forValue($col, $val) : $r->withValue($col, $val);
                }
                $table = $table->withRow($r);
            }
            return $table;
        } catch (PDOException $pdoException) {
            throw new UnableToGetTable(
                message: sprintf('Unable to fetch table "%s", please check previous Exception for more details.', $name),
                previous: $pdoException
            );
        }
    }

    private function generateInsertSqlForParameters(FixtureRecord $fixtureRecord) : string {
        $table = $fixtureRecord->table;
        $parameters = $fixtureRecord->parameters;
        $colsString = implode(
            ', ',
            array_keys($parameters)
        );
        $paramString = implode(
            ', ',
            array_map(static fn(string $col) => ':' . $col, array_keys($parameters))
        );
        return <<<SQL
INSERT INTO $table ($colsString)
VALUES ($paramString)
SQL;
    }

}
