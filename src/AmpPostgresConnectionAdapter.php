<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase;

use Amp\Postgres\PostgresConfig;
use Amp\Postgres\PostgresLink;
use Cspray\DatabaseTestCase\Exception\MissingRequiredComposerPackage;
use function Amp\Postgres\connect;

if (! interface_exists(PostgresLink::class)) {
    throw new MissingRequiredComposerPackage('You must install amphp/postgres to use ' . AmpPostgresConnectionAdapter::class);
}

class AmpPostgresConnectionAdapter extends AbstractConnectionAdapter {

    private ?PostgresLink $connection = null;

    public function __construct(
        private readonly ConnectionAdapterConfig $adapterConfig
    ) {}

    public function establishConnection() : void {
        $this->connection = connect(
            PostgresConfig::fromString(sprintf(
                'db=%s host=%s port=%d user=%s pass=%s',
                $this->adapterConfig->database,
                $this->adapterConfig->host,
                $this->adapterConfig->port,
                $this->adapterConfig->user,
                $this->adapterConfig->password
            ))
        );
    }

    public function onTestStart() : void {
        $this->connection->query('START TRANSACTION');
    }

    public function onTestStop() : void {
        $this->connection->query('ROLLBACK');
    }

    public function closeConnection() : void {
        $this->connection->close();
        $this->connection = null;
    }

    public function getUnderlyingConnection() : PostgresLink {
        return $this->connection;
    }

    protected function executeInsertSql(string $sql, array $parameters) : void {
        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);
    }

    protected function executeSelectAllSql(string $table) : array {
        $result = $this->connection->query(sprintf('SELECT * FROM %s', $table));
        $rows = [];
        while ($row = $result->fetchRow()) {
            $rows[] = $row;
        }
        return $rows;
    }
}
