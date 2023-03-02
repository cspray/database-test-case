<?php

namespace Cspray\DatabaseTestCase;

use Cspray\DatabaseTestCase\DatabaseRepresentation\Table;

interface ConnectionAdapter {

    public function establishConnection() : void;

    public function onTestStart() : void;

    public function onTestStop() : void;

    public function closeConnection() : void;

    public function loadFixture(Fixture $fixture, Fixture... $additionalFixture) : void;

    public function getUnderlyingConnection() : object;

    public function getTable(string $name) : Table;

}