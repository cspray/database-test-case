<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Tests\Unit\DatabaseCleanup;

use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapter;
use Cspray\DatabaseTesting\DatabaseAwareTest;
use Cspray\DatabaseTesting\DatabaseCleanup\TablesToTruncate;
use Cspray\DatabaseTesting\DatabaseCleanup\TruncateTables;
use Phake;
use Phake\IMock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TruncateTables::class)]
final class TruncateTablesTest extends TestCase {

    private TruncateTables $subject;
    private ConnectionAdapter&IMock $connectionAdapter;
    private TablesToTruncate&IMock $tablesToTruncate;
    private DatabaseAwareTest&IMock $databaseAwareTest;

    protected function setUp() : void {
        $this->connectionAdapter = Phake::mock(ConnectionAdapter::class);
        $this->tablesToTruncate = Phake::mock(TablesToTruncate::class);
        $this->databaseAwareTest = Phake::mock(DatabaseAwareTest::class);
        $this->subject = new TruncateTables($this->tablesToTruncate);
    }

    public function testCleanUpBeforeTestTruncatesTablesBasedOnOrderProvidedByTablesToTruncate() : void {
        Phake::when($this->tablesToTruncate)->tables($this->databaseAwareTest)->thenReturn(['foo', 'bar', 'baz']);

        $this->subject->cleanupBeforeTest($this->databaseAwareTest, $this->connectionAdapter);

        Phake::inOrder(
            Phake::verify($this->connectionAdapter)->truncateTable('foo'),
            Phake::verify($this->connectionAdapter)->truncateTable('bar'),
            Phake::verify($this->connectionAdapter)->truncateTable('baz')
        );
    }

    public function testTeardownAfterTestHasNoInteractionWithTheConnectionAdapter() : void {
        $this->subject->teardownAfterTest($this->databaseAwareTest, $this->connectionAdapter);

        Phake::verifyNoInteraction($this->connectionAdapter);
    }
}
