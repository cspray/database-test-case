<?php

namespace Cspray\DatabaseTesting\DatabaseRepresentation;

use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Represents a single table in a relational database.
 *
 * @api
 * @template-extends IteratorAggregate<int, Row>
 */
interface Table extends Countable, IteratorAggregate {

    public function name() : string;

    public function row(int $index) : ?Row;

    public function reload() : void;

}
