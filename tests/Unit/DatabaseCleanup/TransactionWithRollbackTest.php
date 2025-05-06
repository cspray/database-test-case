<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Tests\Unit\DatabaseCleanup;

use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapter;
use Cspray\DatabaseTesting\DatabaseAwareTest;
use Cspray\DatabaseTesting\DatabaseCleanup\TransactionWithRollback;
use Phake;
use Phake\IMock;
use Phake\Mock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(TransactionWithRollback::class)]
final class TransactionWithRollbackTest extends TestCase {

    private TransactionWithRollback $subject;
    private ConnectionAdapter&IMock $connectionAdapter;
    private DatabaseAwareTest&IMock $databaseAwareTest;

    protected function setUp() : void {
        $this->subject = new TransactionWithRollback();
        $this->connectionAdapter = Phake::mock(ConnectionAdapter::class);
        $this->databaseAwareTest = Phake::mock(DatabaseAwareTest::class);
    }

    public function testCleanupDatabaseBeforeTestCallsConnectionAdapterBeginTransaction() : void {
        $this->subject->cleanupBeforeTest($this->databaseAwareTest, $this->connectionAdapter);

        Phake::verify($this->connectionAdapter, Phake::times(1))->beginTransaction();
    }

    public function testTeardownAfterTestCallsConnectionAdapterRollback() : void {
        $this->subject->teardownAfterTest($this->databaseAwareTest, $this->connectionAdapter);

        Phake::verify($this->connectionAdapter, Phake::times(1))->rollback();
    }

}