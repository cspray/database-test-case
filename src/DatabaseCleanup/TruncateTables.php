<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\DatabaseCleanup;

use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapter;
use Cspray\DatabaseTesting\DatabaseAwareTest;
use Override;

final readonly class TruncateTables implements CleanupStrategy {

    public function __construct(
        private TablesToTruncate $tablesToTruncate = new FixtureAwareTablesToTruncate()
    ) {}

    #[Override]
    public function cleanupBeforeTest(DatabaseAwareTest $test, ConnectionAdapter $connectionAdapter) : void {
        foreach ($this->tablesToTruncate->tables($test) as $table) {
            $connectionAdapter->truncateTable($table);
        }
    }

    #[Override]
    public function teardownAfterTest(DatabaseAwareTest $test, ConnectionAdapter $connectionAdapter) : void {
    }
}