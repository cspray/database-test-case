<?php

namespace Cspray\DatabaseTestCase;

interface Fixture {

    /**
     * @return list<FixtureRecord>
     */
    public function getFixtureRecords() : array;

}
