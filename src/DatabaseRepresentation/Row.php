<?php

namespace Cspray\DatabaseTestCase\DatabaseRepresentation;

use IteratorAggregate;
use Traversable;

/**
 * Represents
 */
final class Row implements IteratorAggregate {

    private function __construct(
        private readonly array $columnValues
    ) {}

    public static function forValue(string $name, mixed $value) : self {
        return new self([$name => $value]);
    }

    public function withValue(string $name, mixed $value) : self {
        return new self([...$this->columnValues, $name => $value]);
    }

    public function get(string $name) : mixed {
        return $this->columnValues[$name];
    }

    public function getIterator() : Traversable {
        yield from $this->columnValues;
    }

}
