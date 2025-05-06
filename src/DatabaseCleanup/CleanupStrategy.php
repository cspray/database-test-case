<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\DatabaseCleanup;

use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapter;
use Cspray\DatabaseTesting\DatabaseAwareTest;

interface CleanupStrategy {

    public function cleanupBeforeTest(DatabaseAwareTest $test, ConnectionAdapter $connectionAdapter) : void;

    public function teardownAfterTest(DatabaseAwareTest $test, ConnectionAdapter $connectionAdapter) : void;

}