<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase;

use Cspray\DatabaseTestCase\DatabaseRepresentation\Table;
use Cspray\DatabaseTestCase\Exception\ConnectionNotYetEstablished;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

abstract class DatabaseTestCase extends TestCase {

    private static ?ConnectionAdapter $connectionAdapter;

    final protected static function getUnderlyingConnection() : object {
        if (! isset(self::$connectionAdapter)) {
            throw new ConnectionNotYetEstablished('Attempted to get a connection that has not been established yet. Please ensure the DatabaseTestCase::setupBeforeClass hook runs before calling this method.');
        }
        return self::$connectionAdapter->getUnderlyingConnection();
    }

    final public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
        self::$connectionAdapter = static::getConnectionAdapter();
        self::$connectionAdapter->establishConnection();
        static::beforeAll();
    }

    protected static function beforeAll() : void {

    }

    final protected function setUp() : void {
        parent::setUp();
        self::$connectionAdapter->onTestStart();
        $reflectionMethod = new ReflectionMethod($this, $this->name());
        $loadFixtureAttr = $reflectionMethod->getAttributes(LoadFixture::class, \ReflectionAttribute::IS_INSTANCEOF);
        if ($loadFixtureAttr !== []) {
            $loadFixture = $loadFixtureAttr[0]->newInstance();
            assert($loadFixture instanceof LoadFixture);
            self::$connectionAdapter->loadFixture(...$loadFixture->fixtures);
        }
        $this->beforeEach();
    }

    protected function beforeEach() : void {

    }

    final protected function tearDown() : void {
        parent::tearDown();
        $this->afterEach();
        self::$connectionAdapter->onTestStop();
    }

    protected function afterEach() : void {

    }

    final public static function tearDownAfterClass() : void {
        parent::tearDownAfterClass();
        static::afterAll();
        self::$connectionAdapter->closeConnection();
        self::$connectionAdapter = null;
    }

    protected static function afterAll() : void {
    }

    final protected function getTable(string $name) : Table {
        return self::$connectionAdapter->getTable($name);
    }

    abstract protected static function getConnectionAdapter() : ConnectionAdapter;

}
