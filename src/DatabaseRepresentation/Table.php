<?php

namespace Cspray\DatabaseTestCase\DatabaseRepresentation;

use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Represents a single table in a relational database.
 */
final class Table implements Countable, IteratorAggregate {

    private function __construct(
        private readonly string $name,
        private readonly array $rows = []
    ) {}

    public static function forName(string $name) : self {
        return new self($name);
    }

    public function withRow(Row $row) : self {
        return new self($this->name, [...$this->rows, $row]);
    }

    public function getName() : string {
        return $this->name;
    }

    public function getRow(int $index) : ?Row {
        return $this->rows[$index] ?? null;
    }

    public function getIterator() : Traversable {
        yield from $this->rows;
    }

    public function count() : int {
        return count($this->rows);
    }
}
