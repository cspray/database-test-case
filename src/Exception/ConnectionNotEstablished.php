<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Exception;

class ConnectionNotEstablished extends Exception {

    public static function fromInvalidInvocationBeforeConnectionEstablished(string $classMethod) : self {
        return new self(sprintf(
            'A connection to the test database MUST be established before invoking %s',
            $classMethod
        ));
    }

}