<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Internal;

use Closure;
use Cspray\DatabaseTesting\DatabaseRepresentation\Row;
use Cspray\DatabaseTesting\DatabaseRepresentation\Table;
use Traversable;

/**
 * @internal
 */
final class ClosureDataProviderTable implements Table {

    /**
     * @var list<Row>
     */
    private array $rows = [];

    /**
     * @param Closure():list<array<non-empty-string, mixed>> $dataProvider
     */
    public function __construct(
        private readonly string $name,
        private readonly Closure $dataProvider
    ) {
        $this->reload();
    }

    private function createRow(array $record) : Row {
        return new class($record) implements Row {

            public function __construct(
                private readonly array $record,
            ) {}

            public function get(string $name) : mixed {
                return $this->record[$name];
            }

            public function getIterator() : Traversable {
                yield from $this->record;
            }
        };
    }

    public function getIterator() : Traversable {
        yield from $this->rows;
    }

    public function count() : int {
        return count($this->rows);
    }

    public function name() : string {
        return $this->name;
    }

    public function row(int $index) : ?Row {
        return $this->rows[$index] ?? null;
    }

    public function reload() : void {
        $this->rows = [];
        $records = ($this->dataProvider)();
        foreach ($records as $record) {
            $this->rows[] = $this->createRow($record);
        }
    }
}