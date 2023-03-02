<?php

namespace Cspray\DatabaseTestCase;

use Cspray\DatabaseTestCase\Exception\InvalidFixture;

final class SingleRecordFixture implements Fixture {

    public function __construct(
        private readonly string $table,
        private readonly array $record
    ) {
        if (empty($this->table)) {
            throw new InvalidFixture('A valid table name must be provided when using ' . self::class);
        }

        if (empty($this->record)) {
            throw new InvalidFixture('A valid, non-empty record must be provided when using ' . self::class);
        }
    }

    public function getFixtureRecords() : array {
        return [
            new FixtureRecord($this->table, $this->record)
        ];
    }
}