<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase;

use Cspray\DatabaseTestCase\DatabaseRepresentation\Row;
use Cspray\DatabaseTestCase\DatabaseRepresentation\Table;
use Cspray\DatabaseTestCase\Exception\UnableToGetTable;
use Throwable;

abstract class AbstractConnectionAdapter implements ConnectionAdapter {

    final public function loadFixture(Fixture $fixture, Fixture ...$additionalFixture) : void {
        /** @var Fixture $f */
        foreach ([$fixture, ...$additionalFixture] as $f) {
            foreach ($f->getFixtureRecords() as $fixtureRecord) {
                $sql = $this->generateInsertSqlForParameters($fixtureRecord);
                $parameters = $fixtureRecord->parameters;
                $this->executeInsertSql($sql, $parameters);
            }
        }
    }

    final public function getTable(string $name) : Table {
        try {
            $table = Table::forName($name);
            foreach ($this->executeSelectAllSql($name) as $row) {
                $r = null;
                foreach ($row as $col => $val) {
                    $r = $r === null ? Row::forValue($col, $val) : $r->withValue($col, $val);
                }
                $table = $table->withRow($r);
            }
            return $table;
        } catch (Throwable $throwable) {
            throw new UnableToGetTable(
                message: sprintf('Unable to fetch table "%s", please check previous Exception for more details.', $name),
                previous: $throwable
            );
        }
    }

    protected function generateInsertSqlForParameters(FixtureRecord $fixtureRecord) : string {
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

    abstract protected function executeInsertSql(string $sql, array $parameters) : void;

    /**
     * @return list<array<string, mixed>>
     * @throws Throwable
     */
    abstract protected function executeSelectAllSql(string $table) : array;

}
