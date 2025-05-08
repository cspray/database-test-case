<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting;

use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapter;
use Cspray\DatabaseTesting\ConnectionAdapter\ConnectionAdapterFactory;
use Cspray\DatabaseTesting\DatabaseCleanup\CleanupStrategy;
use Cspray\DatabaseTesting\DatabaseRepresentation\Table;
use Cspray\DatabaseTesting\Exception\ConnectionAlreadyEstablished;
use Cspray\DatabaseTesting\Exception\ConnectionNotEstablished;
use Cspray\DatabaseTesting\Internal\ClosureDataProviderTable;

/**
 * Represents the public API testing framework extensions should interact with to establish test database connections
 * and ensure the state of the database before and after tests.
 *
 * @api
 */
final class TestDatabase {

    private static ?ConnectionAdapter $connectionAdapter = null;

    private function __construct(
        private readonly string $testClass,
        private readonly ConnectionAdapterFactory $connectionAdapterFactory,
        private readonly CleanupStrategy $cleanupStrategy,
    ) {}

    /**
     * @param class-string $class
     * @param RequiresTestDatabaseSettings $requiresTestDatabase
     * @return self
     *@internal
     */
    public static function createFromTestCaseRequiresDatabase(
        string $class,
        RequiresTestDatabaseSettings $requiresTestDatabase
    ) : self {
        return new self(
            $class,
            $requiresTestDatabase->connectionAdapterFactory(),
            $requiresTestDatabase->cleanupStrategy()
        );
    }

    /**
     * Allow for introspection of a database table.
     *
     * @param non-empty-string $name
     * @return Table
     */
    public static function table(string $name) : Table {
        self::verifyConnectionEstablished(__METHOD__);
        return new ClosureDataProviderTable($name, fn() => self::$connectionAdapter->selectAll($name));
    }

    /**
     * @template UnderlyingConnection of object
     * @return UnderlyingConnection
     */
    public static function connection() : object {
        self::verifyConnectionEstablished(__METHOD__);
        return self::$connectionAdapter->underlyingConnection();
    }

    public function establishConnection() : void {
        if (self::$connectionAdapter !== null) {
            throw ConnectionAlreadyEstablished::fromConnectionAlreadyEstablished();
        }
        self::$connectionAdapter = $this->connectionAdapterFactory->createConnectionAdapter();
        self::$connectionAdapter->establishConnection();
    }

    public function prepareForTest(DatabaseAwareTest $databaseAwareTest) : void {
        self::verifyConnectionEstablished(__METHOD__);
        $this->cleanupStrategy->cleanupBeforeTest($databaseAwareTest, self::$connectionAdapter);
        self::$connectionAdapter->insert($databaseAwareTest->fixtures());
    }

    public function cleanupAfterTest(DatabaseAwareTest $databaseAwareTest) : void {
        self::verifyConnectionEstablished(__METHOD__);
        $this->cleanupStrategy->teardownAfterTest($databaseAwareTest, self::$connectionAdapter);
    }

    public function closeConnection() : void {
        self::verifyConnectionEstablished(__METHOD__);
        self::$connectionAdapter->closeConnection();
        self::$connectionAdapter = null;
    }

    private static function verifyConnectionEstablished(string $method) : void {
        if (self::$connectionAdapter === null) {
            throw ConnectionNotEstablished::fromInvalidInvocationBeforeConnectionEstablished($method);
        }
    }

}