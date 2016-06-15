<?php

namespace ServiceInterface\DB\Connection;

/**
 * Interface Connection
 * @package ServiceInterface\DB\Connection
 */
interface Connection
{
    /**
     * @param string $sql
     * @param array $values
     * @return bool
     */
    public function execute($sql, $values = []);
}
