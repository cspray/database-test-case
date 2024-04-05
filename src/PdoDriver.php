<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase;

enum PdoDriver : string{
    case Postgresql = 'pdo_pgsql';
    case Mysql = 'pdo_mysql';
    case Sqlite = 'pdo_sqlite';

    public function dsn(ConnectionAdapterConfig $adapterConfig) : string {
        return match ($this) {
            self::Sqlite => sprintf(
                'sqlite:%s',
                $adapterConfig->host
            ),
            default => sprintf(
                '%s:host=%s;port=%d;dbname=%s;user=%s;password=%s',
                $this->dsnIdentifier(),
                $adapterConfig->host,
                $adapterConfig->port,
                $adapterConfig->database,
                $adapterConfig->user,
                $adapterConfig->password
            ),
        };

    }

    private function dsnIdentifier() : string {
        return match ($this) {
            self::Postgresql => 'pgsql',
            self::Mysql => 'mysql',
            self::Sqlite => 'sqlite',
        };
    }

    public function startTransactionSql() : string {
        return match ($this) {
            self::Sqlite => 'BEGIN TRANSACTION',
            default => 'START TRANSACTION',
        };
    }
}