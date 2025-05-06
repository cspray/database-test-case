<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\DatabaseCleanup;

use Cspray\DatabaseTesting\DatabaseAwareTest;

interface TablesToTruncate {

    /**
     * @return list<non-empty-string>
     */
    public function tables(DatabaseAwareTest $test) : array;

}