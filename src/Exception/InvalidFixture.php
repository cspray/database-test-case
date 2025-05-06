<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Exception;


final class InvalidFixture extends Exception {

    public static function fromEmptyTableName() : self {
        return new self(
            'A valid, non-empty table name must be provided with a Fixture'
        );
    }

    public static function fromEmptyRow() : self {
        return new self(
            'A valid, non-empty record must be provided with a Fixture'
        );
    }

}
