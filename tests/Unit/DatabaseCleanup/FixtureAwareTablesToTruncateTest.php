<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Tests\Unit\DatabaseCleanup;

use Cspray\DatabaseTesting\DatabaseAwareTest;
use Cspray\DatabaseTesting\DatabaseCleanup\FixtureAwareTablesToTruncate;
use Cspray\DatabaseTesting\DatabaseCleanup\ForceTruncateAfterFixtureTables;
use Cspray\DatabaseTesting\DatabaseCleanup\ForceTruncateBeforeFixtureTables;
use Cspray\DatabaseTesting\DatabaseCleanup\ListOfTablesToTruncate;
use Cspray\DatabaseTesting\Fixture\Fixture;
use Cspray\DatabaseTesting\Fixture\FixtureRecord;
use Phake;
use Phake\IMock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FixtureAwareTablesToTruncate::class)]
final class FixtureAwareTablesToTruncateTest extends TestCase {

    private FixtureAwareTablesToTruncate $subject;
    private DatabaseAwareTest&IMock $databaseAwareTest;

    protected function setUp() : void {
        $this->subject = new FixtureAwareTablesToTruncate();
        $this->databaseAwareTest = Phake::mock(DatabaseAwareTest::class);
    }

    public function testFixtureTablesAreTruncatedInReverseOrderByDefault() : void {
        $fixture = Phake::mock(Fixture::class);
        Phake::when($this->databaseAwareTest)->class()->thenReturn(__CLASS__);
        Phake::when($this->databaseAwareTest)->method()->thenReturn(__FUNCTION__);
        Phake::when($this->databaseAwareTest)->fixtures()->thenReturn([$fixture]);
        Phake::when($fixture)->records()->thenReturn([
            new FixtureRecord('foo', []),
            new FixtureRecord('bar', []),
            new FixtureRecord('baz', [])
        ]);

         $tables = $this->subject->tables($this->databaseAwareTest);

         self::assertSame(['baz', 'bar', 'foo'], $tables);
    }

    public function testMultipleEntriesForTableOnlyTruncatesTableOnce() : void {
        $fixture = Phake::mock(Fixture::class) ;
        Phake::when($this->databaseAwareTest)->class()->thenReturn(__CLASS__);
        Phake::when($this->databaseAwareTest)->method()->thenReturn(__FUNCTION__);
        Phake::when($this->databaseAwareTest)->fixtures()->thenReturn([$fixture]);
        Phake::when($fixture)->records()->thenReturn([
            new FixtureRecord('foo', []),
            new FixtureRecord('foo', []),
            new FixtureRecord('bar', []),
        ]);

        $tables = $this->subject->tables($this->databaseAwareTest);

        self::assertSame(['bar', 'foo'], $tables);
    }

    #[ForceTruncateBeforeFixtureTables(new ListOfTablesToTruncate(['before-one', 'before-two', 'before-three']))]
    public function testMethodHasTablesToForceTruncateBeforeFixtureTablesAreProperlySorted() : void {
        $fixture = Phake::mock(Fixture::class);
        Phake::when($this->databaseAwareTest)->class()->thenReturn(__CLASS__);
        Phake::when($this->databaseAwareTest)->method()->thenReturn(__FUNCTION__);
        Phake::when($this->databaseAwareTest)->fixtures()->thenReturn([$fixture]);
        Phake::when($fixture)->records()->thenReturn([
            new FixtureRecord('foo', []),
            new FixtureRecord('bar', []),
            new FixtureRecord('baz', [])
        ]);

        $tables = $this->subject->tables($this->databaseAwareTest);

        self::assertSame(['before-one', 'before-two', 'before-three', 'baz', 'bar', 'foo'], $tables);
    }

    #[ForceTruncateAfterFixtureTables(new ListOfTablesToTruncate(['after-one', 'after-two', 'after-three']))]
    public function testMethodHasTablesToForceTruncateAfterFixtureTablesAreProperlySorted() : void {
        $fixture = Phake::mock(Fixture::class);
        Phake::when($this->databaseAwareTest)->class()->thenReturn(__CLASS__);
        Phake::when($this->databaseAwareTest)->method()->thenReturn(__FUNCTION__);
        Phake::when($this->databaseAwareTest)->fixtures()->thenReturn([$fixture]);
        Phake::when($fixture)->records()->thenReturn([
            new FixtureRecord('foo', []),
            new FixtureRecord('bar', []),
            new FixtureRecord('baz', [])
        ]);

        $tables = $this->subject->tables($this->databaseAwareTest);

        self::assertSame(['baz', 'bar', 'foo', 'after-one', 'after-two', 'after-three'], $tables);
    }

    #[ForceTruncateBeforeFixtureTables(new ListOfTablesToTruncate(['before-one', 'before-two', 'before-three']))]
    #[ForceTruncateAfterFixtureTables(new ListOfTablesToTruncate(['after-one', 'after-two', 'after-three']))]
    public function testMethodHasBeforeAndAfterForceTruncateTablesAreProperlySorted() : void {
        $fixture = Phake::mock(Fixture::class);
        Phake::when($this->databaseAwareTest)->class()->thenReturn(__CLASS__);
        Phake::when($this->databaseAwareTest)->method()->thenReturn(__FUNCTION__);
        Phake::when($this->databaseAwareTest)->fixtures()->thenReturn([$fixture]);
        Phake::when($fixture)->records()->thenReturn([
            new FixtureRecord('foo', []),
            new FixtureRecord('bar', []),
            new FixtureRecord('baz', [])
        ]);

        $tables = $this->subject->tables($this->databaseAwareTest);

        self::assertSame([
            'before-one',
            'before-two',
            'before-three',
            'baz',
            'bar',
            'foo',
            'after-one',
            'after-two',
            'after-three'
        ], $tables);
    }

}