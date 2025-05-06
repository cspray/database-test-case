<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting;

use Cspray\DatabaseTesting\Fixture\Fixture;
use Cspray\DatabaseTesting\Fixture\LoadFixture;

interface DatabaseAwareTest {

    public function class() : string;

    public function method() : string;

    /**
     * @return list<Fixture>
     */
    public function fixtures() : array;

}