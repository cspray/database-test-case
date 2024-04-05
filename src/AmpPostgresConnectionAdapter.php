<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase;

use Amp\Postgres\PostgresConfig;
use Amp\Postgres\PostgresLink;
use Closure;
use Cspray\DatabaseTestCase\Exception\MissingRequiredComposerPackage;
use function Amp\Postgres\connect;

if (! interface_exists(PostgresLink::class)) {
    throw new MissingRequiredComposerPackage('You must install amphp/postgres to use ' . AmpPostgresConnectionAdapter::class);
}

class AmpPostgresConnectionAdapter extends AbstractConnectionAdapter {

    private ?PostgresLink $connection = null;

    private function __construct(
        private readonly Closure $connectionFactory
    ) {}

    public static function fromConnectionConfig(ConnectionAdapterConfig $config) : self {
        return new self(fn() => connect(
            PostgresConfig::fromString(sprintf(
                'db=%s host=%s port=%d user=%s pass=%s',
                $config->database,
                $config->host,
                $config->port,
                $config->user,
                $config->password
            ))
        ));
    }

    public static function fromExistingConnection(PostgresLink $link) : self {
        return new self(fn() => $link);
    }

    public function establishConnection() : void {
        $this->connection = ($this->connectionFactory)();
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
        $this->connection->execute($sql, $parameters);
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
