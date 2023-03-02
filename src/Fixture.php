<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase;

interface Fixture {

    /**
     * @return list<FixtureRecord>
     */
    public function getFixtureRecords() : array;

}
