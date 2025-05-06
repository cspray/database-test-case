<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\ConnectionAdapter;

use SensitiveParameter;

/**
 * @api
 */
class ConnectionAdapterConfig {

    public function __construct(
        public readonly string $database,
        public readonly string $host,
        public readonly int $port,
        public readonly string $user,
        #[SensitiveParameter] public readonly string $password
    ) {}

}