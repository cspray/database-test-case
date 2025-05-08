<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Internal;

use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapter;
use Cspray\DatabaseTesting\Exception\ConnectionNotEstablished;
use Cspray\DatabaseTesting\Fixture\SingleRecordFixture;
use PHPUnit\Framework\TestCase;

/**
 * @api
 * @template DatabaseConnection of object
 */
abstract class ConnectionAdapterTestCase extends TestCase {

    /**
     * @var ConnectionAdapter<DatabaseConnection>
     */
    protected ConnectionAdapter $connectionAdapter;

    /**
     * @return ConnectionAdapter<DatabaseConnection>
     */
    protected abstract function connectionAdapter() : ConnectionAdapter;

    /**
     * @return class-string<DatabaseConnection>
     */
    protected abstract function expectedConnectionType() : string;

    protected abstract function assertConnectionClosed() : void;

    /**
     * @param non-empty-string $table
     * @return list<array<non-empty-string, mixed>>
     */
    protected abstract function executeSelectSql(string $sql) : array;

    protected abstract function executeDeleteSql(string $sql) : void;

    protected function setUp() : void {
        $this->connectionAdapter = $this->connectionAdapter();
    }

    public function testGettingConnectionBeforeEstablishingConnectionThrowsError() : void {
        $this->expectException(ConnectionNotEstablished::class);
        $this->expectExceptionMessage(
            'A connection to the test database MUST be established before invoking ' . $this->connectionAdapter::class . '::underlyingConnection'
        );

        $this->connectionAdapter->underlyingConnection();
    }

    public function testGettingConnectionAfterEstablishingConnectionHasCorrectType() : void {
        $this->connectionAdapter->establishConnection();

        self::assertInstanceOf(
            $this->expectedConnectionType(),
            $this->connectionAdapter->underlyingConnection()
        );
    }

    public function testClosingConnectionBeforeEstablishConnectionThrowsError() : void {
        $this->expectException(ConnectionNotEstablished::class);
        $this->expectExceptionMessage(
            'A connection to the test database MUST be established before invoking ' . $this->connectionAdapter::class . '::closeConnection'
        );

        $this->connectionAdapter->closeConnection();
    }

    public function testClosingConnectionAfterEstablishingConnectionProperlyClosesConnection() : void {
        $this->connectionAdapter->establishConnection();
        $this->connectionAdapter->closeConnection();

        $this->assertConnectionClosed();
    }

    public function testInsertBeforeEstablishingConnectionThrowsError() : void {
        $this->expectException(ConnectionNotEstablished::class);
        $this->expectExceptionMessage(
            'A connection to the test database MUST be established before invoking ' . $this->connectionAdapter::class . '::insert'
        );

        $this->connectionAdapter->insert([
            new SingleRecordFixture('my_table', ['name' => 'Harry'])
        ]);
    }

    public function testInsertAfterEstablishingConnectionResultsInAppropriateRecords() : void {
        $this->connectionAdapter->establishConnection();

        $sql = 'SELECT * FROM my_table';
        $records = $this->executeSelectSql($sql);

        self::assertEmpty($records);

        $this->connectionAdapter->insert([
            new SingleRecordFixture('my_table', ['name' => 'Harry']),
            new SingleRecordFixture('my_table', ['name' => 'Mack']),
            new SingleRecordFixture('my_table', ['name' => 'Sterling'])
        ]);

        $insertedRecords = $this->executeSelectSql($sql);

        self::assertCount(3, $insertedRecords);
        self::assertSame(['Harry', 'Mack', 'Sterling'], array_column($insertedRecords, 'name'));

        $this->executeDeleteSql('DELETE FROM my_table');

        self::assertEmpty($this->executeSelectSql($sql));
    }

    public function testBeginTransactionAndRollbackResultsInRecordsNotPersisted() : void {
        $this->connectionAdapter->establishConnection();

        $sql = 'SELECT * FROM my_table';
        $records = $this->executeSelectSql($sql);

        self::assertEmpty($records);

        $this->connectionAdapter->beginTransaction();

        $this->connectionAdapter->insert([
            new SingleRecordFixture('my_table', ['name' => 'Harry']),
            new SingleRecordFixture('my_table', ['name' => 'Mack']),
            new SingleRecordFixture('my_table', ['name' => 'Sterling'])
        ]);

        $insertedRecords = $this->executeSelectSql($sql);

        self::assertCount(3, $insertedRecords);

        $this->connectionAdapter->rollback();

        self::assertEmpty($this->executeSelectSql($sql));
    }

    public function testTruncateTableEnsuresTableIsClearedOfRecords() : void {
        $this->connectionAdapter->establishConnection();

        $sql = 'SELECT * FROM my_table';
        $records = $this->executeSelectSql($sql);

        self::assertEmpty($records);

        $this->connectionAdapter->insert([
            new SingleRecordFixture('my_table', ['name' => 'Harry']),
            new SingleRecordFixture('my_table', ['name' => 'Mack']),
            new SingleRecordFixture('my_table', ['name' => 'Sterling'])
        ]);

        $insertedRecords = $this->executeSelectSql($sql);

        self::assertCount(3, $insertedRecords);

        $this->connectionAdapter->truncateTable('my_table');

        self::assertEmpty($this->executeSelectSql($sql));
    }

    public function testFetchingTableFromConnectionAdapterAllowsInspectingCorrectRecords() : void {
        $this->connectionAdapter->establishConnection();

        $sql = 'SELECT * FROM my_table';
        $records = $this->executeSelectSql($sql);

        self::assertEmpty($records);

        $this->connectionAdapter->insert([
            new SingleRecordFixture('my_table', ['name' => 'Harry']),
        ]);

        $data = $this->connectionAdapter->selectAll('my_table');

        self::assertCount(1, $data);
        self::assertSame('Harry', $data[0]['name']);

        $this->connectionAdapter->insert([
            new SingleRecordFixture('my_table', ['name' => 'Mack']),
        ]);

        $newData = $this->connectionAdapter->selectAll('my_table');

        self::assertCount(2, $newData);
        self::assertSame('Harry', $newData[0]['name']);
        self::assertSame('Mack', $newData[1]['name']);

        $this->connectionAdapter->truncateTable('my_table');

        $truncatedData = $this->connectionAdapter->selectAll('my_table');

        self::assertCount(0, $truncatedData);
    }


}