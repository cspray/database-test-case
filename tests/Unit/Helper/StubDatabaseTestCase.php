<?php

namespace Cspray\DatabaseTestCase\Tests\Unit\Helper;

use Cspray\DatabaseTestCase\ConnectionAdapter;
use Cspray\DatabaseTestCase\DatabaseTestCase;
use Cspray\DatabaseTestCase\LoadFixture;
use Cspray\DatabaseTestCase\SingleRecordFixture;

class StubDatabaseTestCase extends DatabaseTestCase {

    private static ConnectionAdapter $connectionAdapter;

    private static MethodRecorder $recorder;

    public function __construct(string $name, ConnectionAdapter $connectionAdapter, MethodRecorder $recorder) {
        parent::__construct($name);
        self::$connectionAdapter = $connectionAdapter;
        self::$recorder = $recorder;
    }

    protected static function beforeAll() : void {
        self::$recorder->record(__METHOD__, []);
    }

    protected function beforeEach() : void {
        self::$recorder->record(__METHOD__, []);
    }

    protected function afterEach() : void {
        self::$recorder->record(__METHOD__, []);
    }

    protected static function afterAll() : void {
        self::$recorder->record(__METHOD__, []);
    }

    public function testSomething() : void {
        self::$recorder->record(__METHOD__, []);
        $this->expectNotToPerformAssertions();
    }

    #[LoadFixture(
        new SingleRecordFixture('my_table', ['name' => 'foo']),
        new SingleRecordFixture('my_table', ['name' => 'bar']),
        new SingleRecordFixture('my_table', ['name' => 'baz']),
    )]
    public function testLoadFixtures() : void {
        self::$recorder->record(__METHOD__, []);
        $this->expectNotToPerformAssertions();
    }

    public function callGetUnderlyingConnection() : object {
        return self::getUnderlyingConnection();
    }

    protected static function getConnectionAdapter() : ConnectionAdapter {
        return self::$connectionAdapter;
    }
}