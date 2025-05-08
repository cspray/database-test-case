<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Tests\Unit;

use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapter;
use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapterFactory;
use Cspray\DatabaseTesting\DatabaseAwareTest;
use Cspray\DatabaseTesting\DatabaseCleanup\CleanupStrategy;
use Cspray\DatabaseTesting\DatabaseRepresentation\Table;
use Cspray\DatabaseTesting\Exception\ConnectionAlreadyEstablished;
use Cspray\DatabaseTesting\Exception\ConnectionNotEstablished;
use Cspray\DatabaseTesting\Fixture\Fixture;
use Cspray\DatabaseTesting\RequiresTestDatabaseSettings;
use Cspray\DatabaseTesting\TestDatabase;
use Phake;
use Phake\IMock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Runtime\PropertyGetHook;
use PHPUnit\Framework\TestCase;

#[CoversClass(TestDatabase::class)]
final class TestDatabaseTest extends TestCase {

    protected function setUp() : void {
        // Generally we wouldn't want our test to know this much about the internal state of our subject
        // nor would we want to be using a static property. However, due to the unique constraints present in
        // writing test adapters and how an end-user must be able to access the underlying connection without
        // direct access to the TestDatabase _instance_, we must rely on access to the class for exposing certain
        // portions of the API. In a realistic integration scenario, this should be carried out by the extension/plugin
        // integrating with the testing framework by calling TestDatabase::closeConnection at the appropriate point
        // in that framework's lifecycle
        $reflection = new \ReflectionClass(TestDatabase::class);
        $reflection->setStaticPropertyValue('connectionAdapter', null);
    }

    public function testAttemptingToCallConnectionWithoutProperlyEstablishingConnectionThrowsException() : void {
        $this->expectException(ConnectionNotEstablished::class);
        $this->expectExceptionMessage(
            'A connection to the test database MUST be established before invoking ' . TestDatabase::class . '::connection'
        );

        TestDatabase::connection();
    }

    /**
     * @return array{
     *     0:RequiresTestDatabaseSettings&IMock,
     *     1:ConnectionAdapter&IMock,
     *     2:CleanupStrategy&IMock,
     *     3:ConnectionAdapterFactory&IMock,
     * }
     */
    private function defaultMocks() : array {
        $cleanupStrategy = Phake::mock(CleanupStrategy::class);
        $connectionAdapterFactory = Phake::mock(ConnectionAdapterFactory::class);
        $connectionAdapter = Phake::mock(ConnectionAdapter::class);
        $requiresTestDatabase = Phake::mock(RequiresTestDatabaseSettings::class);

        Phake::when($requiresTestDatabase)->connectionAdapterFactory()->thenReturn($connectionAdapterFactory);
        Phake::when($requiresTestDatabase)->cleanupStrategy()->thenReturn($cleanupStrategy);
        Phake::when($connectionAdapterFactory)->createConnectionAdapter()->thenReturn($connectionAdapter);

        return [$requiresTestDatabase, $connectionAdapter, $cleanupStrategy, $connectionAdapterFactory];
    }

    public function testEstablishingConnectionCreatesConnectionAdapterAndConnectsToTestDatabase() : void {
        [$requiresTestDatabase, $connectionAdapter] = $this->defaultMocks();

        $subject = TestDatabase::createFromTestCaseRequiresDatabase(__CLASS__, $requiresTestDatabase);
        $subject->establishConnection();

        Phake::verify($connectionAdapter, Phake::times(1))->establishConnection();
    }

    public function testCallingEstablishingConnectionMultipleTimesWithoutClosingConnectionThrowsException() : void {
        [$requiresTestDatabase] = $this->defaultMocks();

        $subject = TestDatabase::createFromTestCaseRequiresDatabase(__CLASS__, $requiresTestDatabase);
        $subject->establishConnection();

        $this->expectException(ConnectionAlreadyEstablished::class);
        $this->expectExceptionMessage(
            'Attempting to establish an already established connection. Please ensure you call ' . TestDatabase::class . '::closeConnection before calling establishConnection again.'
        );

        $subject->establishConnection();
    }

    public function testCallingConnectionAfterEstablishingConnectionReturnsTheCorrectConnectionObject() : void {
        [$requiresTestDatabase, $connectionAdapter] = $this->defaultMocks();

        $connection = new \stdClass();
        Phake::when($connectionAdapter)->underlyingConnection()->thenReturn($connection);

        $subject = TestDatabase::createFromTestCaseRequiresDatabase(__CLASS__, $requiresTestDatabase);
        $subject->establishConnection();

        self::assertSame($connection, TestDatabase::connection());
    }

    public function testPrepareForTestWithoutEstablishingConnectionThrowsException() : void {
        [$requiresTestDatabase] = $this->defaultMocks();
        $databaseAwareTest = Phake::mock(DatabaseAwareTest::class);

        $subject = TestDatabase::createFromTestCaseRequiresDatabase(__CLASS__, $requiresTestDatabase);

        $this->expectExceptionMessage(ConnectionNotEstablished::class);
        $this->expectExceptionMessage(
            'A connection to the test database MUST be established before invoking ' . TestDatabase::class . '::prepareForTest'
        );

        $subject->prepareForTest($databaseAwareTest);
    }

    public function testPrepareForTestWithEstablishedConnectionCallsCorrectOperationsInOrder() : void {
        [$requiresTestDatabase, $connectionAdapter, $cleanupStrategy] = $this->defaultMocks();
        $databaseAwareTest = Phake::mock(DatabaseAwareTest::class);
        $fixture = Phake::mock(Fixture::class);
        Phake::when($databaseAwareTest)->fixtures()->thenReturn([$fixture]);

        $subject = TestDatabase::createFromTestCaseRequiresDatabase(__CLASS__, $requiresTestDatabase);

        $subject->establishConnection();
        $subject->prepareForTest($databaseAwareTest);

        Phake::inOrder(
            Phake::verify($connectionAdapter, Phake::times(1))->establishConnection(),
            Phake::verify($cleanupStrategy, Phake::times(1))->cleanupBeforeTest($databaseAwareTest, $connectionAdapter),
            Phake::verify($connectionAdapter, Phake::times(1))->insert([$fixture]),
        );
    }

    public function testCleanupAfterTestWithoutEstablishingConnectionThrowsException() : void {
        [$requiresTestDatabase] = $this->defaultMocks();
        $databaseAwareTest = Phake::mock(DatabaseAwareTest::class);

        $subject = TestDatabase::createFromTestCaseRequiresDatabase(__CLASS__, $requiresTestDatabase);

        $this->expectExceptionMessage(ConnectionNotEstablished::class);
        $this->expectExceptionMessage(
            'A connection to the test database MUST be established before invoking ' . TestDatabase::class . '::cleanupAfterTest'
        );

        $subject->cleanupAfterTest($databaseAwareTest);
    }

    public function testCleanupAfterTestWithEstablishedConnectionsCallsCorrectOperationsInOrder() : void {
        [$requiresTestDatabase, $connectionAdapter, $cleanupStrategy] = $this->defaultMocks();
        $databaseAwareTest = Phake::mock(DatabaseAwareTest::class);
        $fixture = Phake::mock(Fixture::class);
        Phake::when($databaseAwareTest)->fixtures()->thenReturn([$fixture]);

        $subject = TestDatabase::createFromTestCaseRequiresDatabase(__CLASS__, $requiresTestDatabase);
        $subject->establishConnection();
        $subject->cleanupAfterTest($databaseAwareTest);

        Phake::inOrder(
            Phake::verify($connectionAdapter, Phake::times(1))->establishConnection(),
            Phake::verify($cleanupStrategy, Phake::times(1))->teardownAfterTest($databaseAwareTest, $connectionAdapter)
        );
    }

    public function testCloseConnectionWithoutEstablishingConnectionThrowsException() : void {
        [$requiresTestDatabase] = $this->defaultMocks();

        $subject = TestDatabase::createFromTestCaseRequiresDatabase(__CLASS__, $requiresTestDatabase);

        $this->expectExceptionMessage(ConnectionNotEstablished::class);
        $this->expectExceptionMessage(
            'A connection to the test database MUST be established before invoking ' . TestDatabase::class . '::closeConnection'
        );

        $subject->closeConnection();
    }

    public function testCloseConnectionWithEstablishedConnectionCallsCorrectConnectionAdapterMethodAndNullsTheStaticConnectionAdapter() : void {
        [$requiresTestDatabase, $connectionAdapter] = $this->defaultMocks();

        $subject = TestDatabase::createFromTestCaseRequiresDatabase(__CLASS__, $requiresTestDatabase);
        $subject->establishConnection();
        $subject->closeConnection();

        Phake::inOrder(
            Phake::verify($connectionAdapter, Phake::times(1))->establishConnection(),
            Phake::verify($connectionAdapter, Phake::times(1))->closeConnection()
        );

        $reflection = new \ReflectionClass(TestDatabase::class);
        self::assertNull($reflection->getStaticPropertyValue('connectionAdapter'));
    }

    public function testCallingTableWithoutEstablishingConnectionThrowsException() : void {
        $this->expectExceptionMessage(ConnectionNotEstablished::class);
        $this->expectExceptionMessage(
            'A connection to the test database MUST be established before invoking ' . TestDatabase::class . '::table'
        );

        TestDatabase::table('name');
    }

    public function testCallingTableWithEstablishedConnectionReturnsTableFromConnectionAdapterCall() : void {
        [$requiresTestDatabase, $connectionAdapter] = $this->defaultMocks();
        Phake::when($connectionAdapter)->selectAll('table_name')->thenReturn([
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
            ['id' => 3, 'name' => 'baz'],
        ]);

        $subject = TestDatabase::createFromTestCaseRequiresDatabase(__CLASS__, $requiresTestDatabase);
        $subject->establishConnection();

        $actual = TestDatabase::table('table_name');

        self::assertSame('table_name', $actual->name());
        self::assertCount(3, $actual);
        self::assertSame(1, $actual->row(0)->get('id'));
        self::assertSame('foo', $actual->row(0)->get('name'));
        self::assertSame(2, $actual->row(1)->get('id'));
        self::assertSame('bar', $actual->row(1)->get('name'));
        self::assertSame(3, $actual->row(2)->get('id'));
        self::assertSame('baz', $actual->row(2)->get('name'));
    }

}