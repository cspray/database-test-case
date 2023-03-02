<?php

namespace Cspray\DatabaseTestCase\Tests\Unit\DatabaseRepresentation;

use Cspray\DatabaseTestCase\DatabaseRepresentation\Row;
use Cspray\DatabaseTestCase\DatabaseRepresentation\Table;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Table::class)]
final class TableTest extends TestCase {

    public function testForNameReturnsCorrectName() : void {
        $subject = Table::forName('table_name');

        self::assertSame('table_name', $subject->getName());
    }

    public function testForNameDefaultsToEmptyRows() : void {
        $subject = Table::forName('some_table');

        self::assertEmpty(iterator_to_array($subject));
    }

    public function testGetRowWithNoRowsIsNull() : void {
        $subject = Table::forName('something');

        self::assertNull($subject->getRow(0));
    }

    public function testGetRowWithRowsIsRow() : void {
        $subject = Table::forName('something')->withRow($row = Row::forValue('foo', 'bar'));

        self::assertSame($row, $subject->getRow(0));
    }

    public function testWithRowAddsToRows() : void {
        $subject = Table::forName('another_table')->withRow($row = Row::forValue('foo', 'bar'));

        self::assertSame([$row], iterator_to_array($subject));
    }

    public function testWithMultipleRowsReturnsAllOfThem() : void {
        $subject = Table::forName('another_table')
            ->withRow($a = Row::forValue('foo', 'bar'))
            ->withRow($b = Row::forValue('bar', 'baz'))
            ->withRow($c = Row::forValue('baz', 'qux'));

        self::assertSame([$a, $b, $c], iterator_to_array($subject));
    }

}
