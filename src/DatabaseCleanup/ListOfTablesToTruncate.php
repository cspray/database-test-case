<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\DatabaseCleanup;

use Cspray\DatabaseTesting\DatabaseAwareTest;

final readonly class ListOfTablesToTruncate implements TablesToTruncate {

    public function __construct(
        /** @var list<non-empty-string> */
        private array $tables,
    ) {}

    public function tables(DatabaseAwareTest $test) : array {
        return $this->tables;
    }
}