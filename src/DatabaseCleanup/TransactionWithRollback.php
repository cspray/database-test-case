<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\DatabaseCleanup;

use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapter;
use Cspray\DatabaseTesting\DatabaseAwareTest;
use Override;

final readonly class TransactionWithRollback implements CleanupStrategy {

    #[Override]
    public function cleanupBeforeTest(DatabaseAwareTest $test, ConnectionAdapter $connectionAdapter) : void {
        $connectionAdapter->beginTransaction();
    }

    #[Override]
    public function teardownAfterTest(DatabaseAwareTest $test, ConnectionAdapter $connectionAdapter) : void {
        $connectionAdapter->rollback();
    }
}