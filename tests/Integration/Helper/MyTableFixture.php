<?php

namespace Cspray\DatabaseTestCase\Tests\Integration\Helper;

use Cspray\DatabaseTestCase\Fixture;
use Cspray\DatabaseTestCase\FixtureRecord;

final class MyTableFixture implements Fixture {

    public function __construct(
        private readonly string $name
    ) {}

    public function getFixtureRecords() : array {
        return [
            new FixtureRecord('my_table', ['name' => $this->name])
        ];
    }
}