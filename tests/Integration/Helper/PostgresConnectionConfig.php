<?php

namespace Cspray\DatabaseTestCase\Tests\Integration\Helper;

use Cspray\DatabaseTestCase\ConnectionAdapterConfig;

final class PostgresConnectionConfig extends ConnectionAdapterConfig {

    public function __construct() {
        parent::__construct(
            'postgres',
            'postgres',
            5432,
            'postgres',
            'postgres'
        );
    }

}