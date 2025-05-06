<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Tests\Unit\Internal;

use Cspray\DatabaseTesting\DatabaseRepresentation\Row;
use Cspray\DatabaseTesting\Internal\ClosureDataProviderTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ClosureDataProviderTable::class)]
final class ClosureDataProviderTableTest extends TestCase {

    public function testTableNameIsInjectedValue() : void {
        $subject = new ClosureDataProviderTable(
            'my_table_name',
            fn() => []
        );

        self::assertSame('my_table_name', $subject->name());
    }

    public function testFetchingRowThatDoesNotExistReturnsNull() : void {
        $subject = new ClosureDataProviderTable(
            'ny_table',
            fn() => []
        );

        self::assertNull($subject->row(0));
    }

    public function testFetchingRowThatDoesExistsReturnsRowInstance() : void {
        $subject = new ClosureDataProviderTable(
            'my_table',
            fn() => [
                ['name' => 'Harry', 'profession' => 'goat', 'revolutionary' => true]
            ]
        );

        self::assertInstanceOf(Row::class, $subject->row(0));
        self::assertSame('Harry', $subject->row(0)->get('name'));
        self::assertSame('goat', $subject->row(0)->get('profession'));
        self::assertSame(true, $subject->row(0)->get('revolutionary'));
    }

    public function testFetchingRowIteratesProperlyOverValues() : void {
        $subject = new ClosureDataProviderTable(
            'my_table',
            fn() => [
                ['name' => 'Harry', 'profession' => 'goat', 'revolutionary' => true]
            ]
        );

        self::assertSame(
            ['name' => 'Harry', 'profession' => 'goat', 'revolutionary' => true],
            iterator_to_array($subject->row(0))
        );
    }

    public function testCountingTableReturnsCorrectNumberForAmountOfRecords() : void {
        $subject = new ClosureDataProviderTable(
            'my_table',
            fn() => [
                ['name' => 'Sterling'],
                ['name' => 'Lana'],
                ['name' => 'Cyril']
            ]
        );

        self::assertCount(3, $subject);
    }

    public function testIteratingOverTableHasCorrectCountOfRecords() : void {
        $subject = new ClosureDataProviderTable(
            'my_table',
            fn() => [
                ['name' => 'Sterling'],
                ['name' => 'Lana'],
                ['name' => 'Cyril']
            ]
        );

        self::assertCount(3, iterator_to_array($subject));
    }

    public function testReloadingFetchesNewRowsFromDataProvider() : void {
        $data = new stdClass();
        $data->counter = 0;
        $subject = new ClosureDataProviderTable(
            'my_table',
            fn() => [
                ['counter' => ++$data->counter]
            ]
        );

        self::assertCount(1, $subject);
        self::assertSame(1, $subject->row(0)->get('counter'));

        $subject->reload();

        self::assertCount(1, $subject);
        self::assertSame(2, $subject->row(0)->get('counter'));
    }

}