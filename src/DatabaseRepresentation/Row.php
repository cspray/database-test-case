<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\DatabaseRepresentation;

use IteratorAggregate;
use Traversable;

/**
 * @api
 * @template-extends IteratorAggregate<non-empty-string, mixed>
 */
interface Row extends IteratorAggregate {

    public function get(string $name) : mixed;

    public function getIterator() : Traversable;

}
