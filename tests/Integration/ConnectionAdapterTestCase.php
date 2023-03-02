<?php

namespace Cspray\DatabaseTestCase\Tests\Integration;

use Cspray\DatabaseTestCase\DatabaseRepresentation\Row;
use Cspray\DatabaseTestCase\DatabaseTestCase;
use Cspray\DatabaseTestCase\Exception\UnableToGetTable;
use Cspray\DatabaseTestCase\LoadFixture;
use Cspray\DatabaseTestCase\Tests\Integration\Helper\MyTableFixture;

abstract class ConnectionAdapterTestCase extends DatabaseTestCase {

    abstract protected function getExpectedUnderlyingConnectionClassName() : string;

    abstract protected function executeCountSql(string $table) : int;

    public function testUnderlyingConnectionIsInstanceOfPdo() : void {
        self::assertInstanceOf($this->getExpectedUnderlyingConnectionClassName(), self::getUnderlyingConnection());
    }

    public function testMethodWithNoFixtureHasEmptyTable() : void {
        $tableCount = $this->executeCountSql('my_table');
        self::assertSame(0, $tableCount, 'Expected to start with a fresh table on each test but did not');
    }

    #[LoadFixture(
        new MyTableFixture('Charles'),
        new MyTableFixture('Dyana'),
        new MyTableFixture('Nick')
    )]
    public function testLoadingFixtures() : void {
        $tableCount = $this->executeCountSql('my_table');
        self::assertSame(3, $tableCount, 'Expected to have table loaded with records from fixture');
    }

    public function testGetTableDoesNotExistThrowsException() : void {
        $this->expectException(UnableToGetTable::class);
        $this->expectExceptionMessage('Unable to fetch table "not_a_db_table", please check previous Exception for more details.');

        $this->getTable('not_a_db_table');
    }

    public function testGetTableWithNoRecordsHasEmptyTable() : void {
        $table = $this->getTable('my_table');

        self::assertSame('my_table', $table->getName());
        self::assertEmpty($table);
    }

    #[LoadFixture(
        new MyTableFixture('Charles'),
        new MyTableFixture('Dyana'),
        new MyTableFixture('Nick')
    )]
    public function testGettingTableWithLoadedFixturesReturnsCorrectRowCount() : void {
        $tableCount = $this->executeCountSql('my_table');
        self::assertSame(3, $tableCount, 'Expected to have table loaded with records from fixture');

        $table = $this->getTable('my_table');

        self::assertCount(3, $table);
        self::assertContainsOnlyInstancesOf(Row::class, iterator_to_array($table));
    }

    #[LoadFixture(
        new MyTableFixture('Harry Mack'),
    )]
    public function testGettingTableWithLoadedFixtureReturnsCorrectData() : void {
        $table = $this->getTable('my_table');

        self::assertCount(1, $table);
        $row = $table->getRow(0);
        self::assertNotNull($row->get('id'));
        self::assertSame('Harry Mack', $row->get('name'));
        self::assertNotNull($row->get('created_at'));
    }

}