<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Exception;

use Cspray\DatabaseTesting\TestDatabase;

class ConnectionAlreadyEstablished extends Exception {

    public static function fromConnectionAlreadyEstablished() : self {
        return new self(
            'Attempting to establish an already established connection. Please ensure you call '
            . TestDatabase::class . '::closeConnection before calling establishConnection again.'
        );
    }

}