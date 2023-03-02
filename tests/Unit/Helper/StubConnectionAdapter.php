<?php

namespace Cspray\DatabaseTestCase\Tests\Unit\Helper;

use Cspray\DatabaseTestCase\ConnectionAdapter;
use Cspray\DatabaseTestCase\DatabaseRepresentation\Table;
use Cspray\DatabaseTestCase\Fixture;

class StubConnectionAdapter implements ConnectionAdapter {

    public function __construct(
        private readonly MethodRecorder $recorder
    ) {}

    public function establishConnection() : void {
        $this->recorder->record(__METHOD__, []);
    }

    public function onTestStart() : void {
        $this->recorder->record(__METHOD__, []);
    }

    public function onTestStop() : void {
        $this->recorder->record(__METHOD__, []);
    }

    public function closeConnection() : void {
        $this->recorder->record(__METHOD__, []);
    }

    public function loadFixture(Fixture $fixture, Fixture... $fixtures) : void {
        $this->recorder->record(__METHOD__, [$fixture, ...$fixtures]);
    }

    public function getUnderlyingConnection() : object {
        $class = new \stdClass();
        $class->from = self::class;
        return $class;
    }

    public function getTable(string $name) : Table {
        // TODO: Implement getTable() method.
    }
}