<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Tests\Unit\Fixture;

use Cspray\DatabaseTesting\Exception\InvalidFixture;
use Cspray\DatabaseTesting\Fixture\SingleRecordFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SingleRecordFixture::class)]
final class GenericSqlFixtureTest extends TestCase {

    public function testEmptyTableNameThrowsException() : void {
        $this->expectException(InvalidFixture::class);
        $this->expectExceptionMessage('A valid, non-empty table name must be provided with a Fixture');

        new SingleRecordFixture('', []);
    }

    public function testEmptyRecordThrowsException() : void {
        $this->expectException(InvalidFixture::class);
        $this->expectExceptionMessage('A valid, non-empty record must be provided with a Fixture');

        new SingleRecordFixture('some_table', []);
    }

    public function testFixtureRecordHasFixtureConstructorParameters() : void {
        $subject = new SingleRecordFixture('another_table', ['foo' => 'bar', 'bar' => 'baz']);

        self::assertCount(1, $subject->records());
        self::assertSame('another_table', $subject->records()[0]->table);
        self::assertSame(['foo' => 'bar', 'bar' => 'baz'], $subject->records()[0]->parameters);
    }

}
