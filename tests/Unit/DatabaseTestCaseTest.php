<?php

namespace Cspray\DatabaseTestCase\Tests\Unit;

use Cspray\DatabaseTestCase\DatabaseTestCase;
use Cspray\DatabaseTestCase\Exception\ConnectionNotYetEstablished;
use Cspray\DatabaseTestCase\Fixture;
use Cspray\DatabaseTestCase\Tests\Unit\Helper\MethodRecorder;
use Cspray\DatabaseTestCase\Tests\Unit\Helper\StubConnectionAdapter;
use Cspray\DatabaseTestCase\Tests\Unit\Helper\StubDatabaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DatabaseTestCase::class)]
class DatabaseTestCaseTest extends TestCase {

    private StubConnectionAdapter $adapter;

    private MethodRecorder $recorder;

    protected function setUp() : void {
        $this->recorder = new MethodRecorder();
        $this->adapter = new StubConnectionAdapter($this->recorder);
    }

    private function executeSubject(DatabaseTestCase $subject) : void {
        $subject::setUpBeforeClass();
        $subject->run();
        $subject::tearDownAfterClass();
    }

    public function testConnectionMethodsCalledInCorrectOrder() : void {
        $subject = new StubDatabaseTestCase('testSomething', $this->adapter, $this->recorder);
        self::assertEmpty($this->recorder->getRecordedCalls());
        $this->executeSubject($subject);
        self::assertSame([
            [StubConnectionAdapter::class . '::establishConnection', []],
            [StubDatabaseTestCase::class . '::beforeAll', []],
            [StubConnectionAdapter::class . '::onTestStart', []],
            [StubDatabaseTestCase::class . '::beforeEach', []],
            [StubDatabaseTestCase::class . '::testSomething', []],
            [StubDatabaseTestCase::class . '::afterEach', []],
            [StubConnectionAdapter::class . '::onTestStop', []],
            [StubDatabaseTestCase::class . '::afterAll', []],
            [StubConnectionAdapter::class . '::closeConnection', []]
        ], $this->recorder->getRecordedCalls());
    }

    public function testGetUnderlyingConnectionBeforeConnectionEstablishedThrowsException() : void {
        $subject = new StubDatabaseTestCase('testSomething', $this->adapter, $this->recorder);

        $this->expectException(ConnectionNotYetEstablished::class);
        $this->expectExceptionMessage('Attempted to get a connection that has not been established yet. Please ensure the DatabaseTestCase::setupBeforeClass hook runs before calling this method.');

        $subject->callGetUnderlyingConnection();
    }

    public function testGetUnderlyingConnectionAfterConnectionEstablishedReturnsCorrectObject() : void {
        $subject = new StubDatabaseTestCase('testSomething', $this->adapter, $this->recorder);

        $subject::setUpBeforeClass();

        $connection = $subject->callGetUnderlyingConnection();

        self::assertInstanceOf(\stdClass::class, $connection);
        self::assertSame(StubConnectionAdapter::class, $connection->from);

        $subject::tearDownAfterClass();

        $this->expectException(ConnectionNotYetEstablished::class);
        $this->expectExceptionMessage('Attempted to get a connection that has not been established yet. Please ensure the DatabaseTestCase::setupBeforeClass hook runs before calling this method.');

        $subject->callGetUnderlyingConnection();
    }

    public function testLoadingFixtures() : void {
        $subject = new StubDatabaseTestCase('testLoadFixtures', $this->adapter, $this->recorder);

        $this->executeSubject($subject);

        $calls = $this->recorder->getRecordedCalls();

        self::assertCount(10, $calls);
        self::assertSame(StubConnectionAdapter::class . '::loadFixture', $calls[3][0]);
        self::assertSame(
            [['name' => 'foo'], ['name' => 'bar'], ['name' => 'baz']],
            array_map(static fn(Fixture $fixture) => $fixture->getFixtureRecords()[0]->parameters, $calls[3][1])
        );
    }

}