<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase;

enum PdoDriver : string{
    case Postgresql = 'pdo_pgsql';
    case Mysql = 'pdo_mysql';

    public function getDsnIdentifier() : string {
        return match ($this) {
            self::Postgresql => 'pgsql',
            self::Mysql => 'mysql'
        };
    }
}