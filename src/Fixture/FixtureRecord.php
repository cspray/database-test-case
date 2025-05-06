<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Fixture;

final class FixtureRecord {

    public function __construct(
        public readonly string $table,
        public readonly array $parameters
    ) {}

}