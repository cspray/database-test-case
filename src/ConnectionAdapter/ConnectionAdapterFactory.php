<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\ConnectionAdapter;

/**
 * @api
 * @template UnderlyingConnection of object
 */
interface ConnectionAdapterFactory {

    /**
     * @return ConnectionAdapter<UnderlyingConnection>
     */
    public function createConnectionAdapter() : ConnectionAdapter;

}
