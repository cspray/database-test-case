<?php

namespace Cspray\DatabaseTestCase;

enum PdoDriver : string{
    case Postgresql = 'pdo_pgsql';

    public function getDsnIdentifier() : string {
        return match ($this) {
            self::Postgresql => 'pgsql'
        };
    }
}