<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase;

use Cspray\DatabaseTestCase\DatabaseRepresentation\Row;
use Cspray\DatabaseTestCase\DatabaseRepresentation\Table;
use Cspray\DatabaseTestCase\Exception\UnableToGetTable;
use Cspray\DatabaseTestCase\Exception\MissingRequiredExtension;
use PDO;
use PDOException;

if (! extension_loaded('pdo')) {
    throw new MissingRequiredExtension('You must enable ext-pdo to use the ' . PdoConnectionAdapter::class);
}

final class PdoConnectionAdapter extends AbstractConnectionAdapter {

    private ?PDO $connection = null;

    public function __construct(
        private readonly ConnectionAdapterConfig $adapterConfig,
        private readonly PdoDriver $pdoDriver
    ) {}

    public function establishConnection() : void {
        $this->connection = new PDO(
            sprintf(
                '%s:host=%s;port=%d;dbname=%s;user=%s;password=%s',
                $this->pdoDriver->getDsnIdentifier(),
                $this->adapterConfig->host,
                $this->adapterConfig->port,
                $this->adapterConfig->database,
                $this->adapterConfig->user,
                $this->adapterConfig->password
            )
        );
    }

    public function onTestStart() : void {
        $this->connection->query('START TRANSACTION');
    }

    public function onTestStop() : void {
        $this->connection->query('ROLLBACK');
    }

    public function closeConnection() : void {
        unset($this->connection);
        $this->connection = null;
    }


    public function getUnderlyingConnection() : PDO {
        return $this->connection;
    }


    protected function executeInsertSql(string $sql, array $parameters) : void {
        $statement = $this->getUnderlyingConnection()->prepare($sql);
        foreach ($parameters as $col => $val) {
            $statement->bindValue($col, $val);
        }
        $statement->execute();
    }

    protected function executeSelectAllSql(string $table) : array {
        $query = sprintf('SELECT * FROM %s', $table);
        return $this->connection->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
}
