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

    /** @var callable */
    private $pdoSupplier;

    private readonly PdoDriver $pdoDriver;

    private function __construct(callable $pdoSupplier, PdoDriver $pdoDriver) {
        $this->pdoSupplier = $pdoSupplier;
        $this->pdoDriver = $pdoDriver;
    }

    public static function fromConnectionConfig(ConnectionAdapterConfig $adapterConfig, PdoDriver $pdoDriver) : self {
        return self::fromExistingConnection(new PDO($pdoDriver->dsn($adapterConfig)), $pdoDriver);
    }

    public static function fromExistingConnection(PDO $pdo, PdoDriver $pdoDriver) : self {
        return new self(static fn() => $pdo, $pdoDriver);
    }

    public function establishConnection() : void {
        $this->connection = ($this->pdoSupplier)();
    }

    public function onTestStart() : void {
        $this->connection->query($this->pdoDriver->startTransactionSql());
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
