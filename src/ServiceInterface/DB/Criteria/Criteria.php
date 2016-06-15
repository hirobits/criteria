<?php

namespace ServiceInterface\DB\Criteria;

/**
 * Interface Criteria
 * @package ServiceInterface\DB\Criteria
 */
interface Criteria
{
    /**
     * @param $table
     * @param $column
     * @param $table2
     * @param $column2
     * @param string $op
     * @return mixed
     */
    public function join($table, $column, $table2, $column2, $op = '=');

    /**
     * @param $table
     * @param $column
     * @param array $values
     * @param string $type
     * @return mixed
     */
    public function where($table, $column, $values = [], $type = '=');

    /**
     * @param $query
     * @param array $values
     * @return mixed
     */
    public function whereRaw($query, $values = []);

    /**
     * @param $table
     * @param $column
     * @return mixed
     */
    public function group($table, $column);

    /**
     * @param $table
     * @param $column
     * @param $type
     * @return mixed
     */
    public function order($table, $column, $type);

    /**
     * @param $limit
     * @param int $offset
     * @return mixed
     */
    public function limit($limit, $offset = 0);

    /**
     * @return mixed
     */
    public function buildSelectQuery();

    /**
     * @param $values
     * @return mixed
     */
    public function buildInsertQuery($values);

    /**
     * @param $set_values
     * @return mixed
     */
    public function buildUpdateQuery($set_values);

    /**
     * @return mixed
     */
    public function buildDeleteQuery();
}
