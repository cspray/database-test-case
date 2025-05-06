<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Tests\Unit\Internal;

use Cspray\DatabaseTesting\DatabaseAwareTest;
use Cspray\DatabaseTesting\Fixture\Fixture;
use Cspray\DatabaseTesting\Fixture\LoadFixture;
use Cspray\DatabaseTesting\Fixture\SingleRecordFixture;
use Cspray\DatabaseTesting\Internal\FixtureAttributeAwareDatabaseTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DatabaseAwareTest::class)]
final class FixtureAttributeAwareDatabaseTestTest extends TestCase {

    public function testDatabaseAwareTestMethodReturnsInjectedClassAndMethod() : void {
        $subject = FixtureAttributeAwareDatabaseTest::fromTestMethodWithPossibleFixtures(__CLASS__, __FUNCTION__);

        self::assertSame(__CLASS__, $subject->class());
        self::assertSame(__FUNCTION__, $subject->method());
    }

    public function testDatabaseAwareTestMethodWithNoLoadFixtureAttributesWillReturnEmptyCollection() : void {
        $subject = FixtureAttributeAwareDatabaseTest::fromTestMethodWithPossibleFixtures(__CLASS__, __FUNCTION__);

        self::assertSame([], $subject->fixtures());
    }

    #[LoadFixture(new SingleRecordFixture('table', ['foo' => 'bar']))]
    public function testDatabaseAwareTestMethodWithLoadFixtureAttributeHasCorrectFixturesInCollection() : void {
        $subject = FixtureAttributeAwareDatabaseTest::fromTestMethodWithPossibleFixtures(__CLASS__, __FUNCTION__);

        /** @var list<Fixture> $fixtures */
        $fixtures = $subject->fixtures();
        self::assertCount(1, $fixtures);
        self::assertContainsOnlyInstancesOf(Fixture::class, $fixtures);

        $records = $fixtures[0]->records();
        self::assertCount(1, $records);
        self::assertSame('table', $records[0]->table);
        self::assertSame(['foo' => 'bar'], $records[0]->parameters);
    }

}
