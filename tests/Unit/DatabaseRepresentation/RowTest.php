<?php

namespace Cspray\DatabaseTestCase\Tests\Unit\DatabaseRepresentation;

use Cspray\DatabaseTestCase\DatabaseRepresentation\Row;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Row::class)]
final class RowTest extends TestCase {

    public function testCreatingRowWithColumnValueIsRetrievable() : void {
        $subject = Row::forValue('column', 'foobar');

        self::assertSame('foobar', $subject->get('column'));
    }

    public function testWithColumnValueIsImmutable() : void {
        $a = Row::forValue('column', 'foo');
        $b = $a->withValue('second_column', 'bar');

        self::assertNotSame($a, $b);
    }

    public function testWithColumnValueHasCorrectValue() : void {
        $subject = Row::forValue('first', 'foo')->withValue('second', 'bar');

        self::assertSame('foo', $subject->get('first'));
        self::assertSame('bar', $subject->get('second'));
    }

    public function testIterateOverAddedValues() : void {
        $subject = Row::forValue('first', 'foo')
            ->withValue('second', 'bar')
            ->withValue('third', 'baz');

        self::assertSame([
            'first' => 'foo',
            'second' => 'bar',
            'third' => 'baz',
        ], iterator_to_array($subject));
    }


}