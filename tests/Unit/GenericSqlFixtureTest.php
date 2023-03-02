<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase\Tests\Unit;

use Cspray\DatabaseTestCase\Exception\InvalidFixture;
use Cspray\DatabaseTestCase\SingleRecordFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SingleRecordFixture::class)]
final class GenericSqlFixtureTest extends TestCase {

    public function testEmptyTableNameThrowsException() : void {
        $this->expectException(InvalidFixture::class);
        $this->expectExceptionMessage('A valid table name must be provided when using ' . SingleRecordFixture::class);

        new SingleRecordFixture('', []);
    }

    public function testEmptyRecordThrowsException() : void {
        $this->expectException(InvalidFixture::class);
        $this->expectExceptionMessage('A valid, non-empty record must be provided when using ' . SingleRecordFixture::class);

        new SingleRecordFixture('some_table', []);
    }

    public function testFixtureRecordHasFixtureConstructorParameters() : void {
        $subject = new SingleRecordFixture('another_table', ['foo' => 'bar', 'bar' => 'baz']);

        self::assertCount(1, $subject->getFixtureRecords());
        self::assertSame('another_table', $subject->getFixtureRecords()[0]->table);
        self::assertSame(['foo' => 'bar', 'bar' => 'baz'], $subject->getFixtureRecords()[0]->parameters);
    }

}
