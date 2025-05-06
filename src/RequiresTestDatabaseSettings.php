<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting;

use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapterFactory;
use Cspray\DatabaseTesting\DatabaseCleanup\CleanupStrategy;

interface RequiresTestDatabaseSettings {

    public function connectionAdapterFactory() : ConnectionAdapterFactory;

    public function cleanupStrategy() : CleanupStrategy;

}