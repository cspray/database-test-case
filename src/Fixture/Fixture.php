<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Fixture;

interface Fixture {

    /**
     * @return list<FixtureRecord>
     */
    public function records() : array;

}
