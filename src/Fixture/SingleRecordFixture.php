<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Fixture;

use Cspray\DatabaseTesting\Exception\InvalidFixture;

final class SingleRecordFixture implements Fixture {

    public function __construct(
        private readonly string $table,
        private readonly array $record
    ) {
        if (empty($this->table)) {
            throw InvalidFixture::fromEmptyTableName();
        }

        if (empty($this->record)) {
            throw InvalidFixture::fromEmptyRow();
        }
    }

    public function records() : array {
        return [
            new FixtureRecord($this->table, $this->record)
        ];
    }
}