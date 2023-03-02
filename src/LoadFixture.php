<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class LoadFixture {

    /** @var list<Fixture> */
    public readonly array $fixtures;

    public function __construct(
        Fixture $fixture,
        Fixture... $additionalFixtures
    ) {
        $this->fixtures = [$fixture, ...$additionalFixtures];
    }

}
