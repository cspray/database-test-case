<?php

namespace Cspray\DatabaseTestCase;

class ConnectionAdapterConfig {

    public function __construct(
        public readonly string $database,
        public readonly string $host,
        public readonly int $port,
        public readonly string $user,
        public readonly string $password
    ) {}

}