<?php

namespace Driver\DB\Connection;

use ServiceInterface\DB\Connection\Connection;

/**
 * Class PdoConnection
 * @package Driver\DB\Connection
 */
class PdoConnection implements Connection
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * PdoConnection constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param $sql
     * @param array $values
     * @return bool
     */
    public function execute($sql, $values = [])
    {
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($values);
    }

    /**
     * @param $sql
     * @param array $values
     * @return bool
     */
    public function find($sql, $values = [])
    {
        $stmt = $this->pdo->prepare($sql);

        $result = $stmt->execute($values);

        if (!$result) {
            return false;
        }
        
        return $stmt->fetch();
    }

    /**
     * @param $sql
     * @param array $values
     * @return bool
     */
    public function findAll($sql, $values = [])
    {
        $stmt = $this->pdo->prepare($sql);
        
        $result = $stmt->execute($values);

        if (!$result) {
            return false;
        }
        
        return $stmt->fetchAll();
    }
}
