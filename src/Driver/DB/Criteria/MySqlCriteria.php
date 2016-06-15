<?php

namespace Driver\DB\Criteria;

use ServiceInterface\DB\Criteria\Criteria;

/**
 * Class MySqlCriteria
 * @package Driver\DB\Criteria
 */
class MySqlCriteria implements Criteria
{
    /**
     * @var string
     */
    private $table;
    /**
     * @var array
     */
    private $columns = [];
    /**
     * @var array
     */
    private $joins = [];
    /**
     * @var array
     */
    private $wheres = [];
    /**
     * @var array
     */
    private $values = [];
    /**
     * @var array
     */
    private $groups = [];
    /**
     * @var array
     */
    private $orders = [];
    /**
     * @var int
     */
    private $limit = 100;
    /**
     * @var int
     */
    private $offset = 0;

    /**
     * MySqlCriteria constructor.
     * @param $connection
     * @param $table
     */
    public function __construct($connection, $table)
    {
        $this->from($table);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param string $table
     */
    public function from($table)
    {
        $this->table = $table;
    }

    /**
     * @param string $table
     * @param string $column
     */
    public function column($table, $column)
    {
        $this->columns[] = $table . '.' . $column;
    }

    /**
     * @param string $table
     * @param array $cols
     */
    public function columns($table, $cols)
    {
        foreach ($cols as $col) {
            $this->column($table, $col);
        }
    }

    /**
     * @param $table
     * @param $column
     * @param $table2
     * @param $column2
     * @param string $op
     */
    public function join($table, $column, $table2, $column2, $op = '=')
    {
        $this->joins[] = ' LEFT JOIN ' . $table . '.' . $column . $op . $table2 . '.' . $column2 . ' ';
    }

    /**
     * @param $table
     * @param $column
     * @param array $values
     * @param string $type
     * @throws \Exception
     */
    public function where($table, $column, $values = [], $type = '=')
    {
        $query = $this->whereQuery($table, $column, $values, $type);
        $this->whereRaw($query, $values);
    }

    /**
     * @param $query
     * @param array $values
     */
    public function whereRaw($query, $values = [])
    {
        $this->wheres[] = $query;
        $this->addValues($values);
    }

    /**
     * @param $table
     * @param $column
     * @param array $values
     * @param string $type
     * @return string
     * @throws \Exception
     */
    private function whereQuery($table, $column, $values = [], $type = '=')
    {
        switch ($type) {
            case '=':
                return ' ' . $table . '.' . $column . ' = ? ';

            case '!=':
                return ' ' . $table . '.' . $column . ' != ? ';

            case '>=':
                return ' ' . $table . '.' . $column . ' >= ? ';

            case '>':
                return ' ' . $table . '.' . $column . ' > ? ';

            case '<=':
                return ' ' . $table . '.' . $column . ' <= ? ';

            case '<':
                return ' ' . $table . '.' . $column . ' < ? ';

            case 'IN':
                return ' ' . $table . '.' . $column . ' IN (' . $this->buildPrepareColumns($values) . ') ';

            case 'IS_NULL':
                return ' ' . $table . '.' . $column . ' IS NULL ';

            case 'IS_NOT_NULL':
                return ' ' . $table . '.' . $column . ' IS NOT NULL ';

            default:
                throw new \Exception; // TODO
        }
    }

    /**
     * @param $table
     * @param $column
     */
    public function group($table, $column)
    {
        $this->groups[] = $table . '.' . $column;
    }

    /**
     * @param $table
     * @param $column
     * @param string $type
     */
    public function order($table, $column, $type = 'ASC')
    {
        $this->orders[] = $table . '.' . $column . ' ' . $type;
    }

    /**
     * @param $limit
     * @param int $offset
     */
    public function limit($limit, $offset = 0)
    {
        $this->limit  = $limit;
        $this->offset = $offset;
    }

    /**
     * @param int $offset
     */
    public function offset($offset = 0)
    {
        $this->offset = $offset;
    }

    /**
     * @param $values
     */
    private function addValues($values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $v) {
            $this->values[] = $v;
        }
    }

    /**
     * @param $values
     */
    private function resetValues($values)
    {
        $this->values = $values;
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    public function buildSelectQuery()
    {
        $sql = "SELECT {COLUMNS} FROM {TABLE} {JOIN} {WHERE} {GROUP} {ORDER} {LIMIT}";

        $sql = str_replace("{COLUMNS}", $this->buildSelectColumns(), $sql);
        $sql = str_replace("{TABLE}", $this->buildSelectTable(), $sql);
        $sql = str_replace("{JOIN}", $this->buildSelectJoin(), $sql);
        $sql = str_replace("{WHERE}", $this->buildSelectWhere(), $sql);
        $sql = str_replace("{GROUP}", $this->buildSelectGroup(), $sql);
        $sql = str_replace("{ORDER}", $this->buildSelectOrder(), $sql);
        $sql = str_replace("{LIMIT}", $this->buildSelectLimit(), $sql);

        return $sql;
    }

    /**
     * @return string
     */
    private function buildSelectColumns()
    {
        if (!$this->columns) {
            $this->columns = ['*'];
        }

        return implode(',', $this->columns);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function buildSelectTable()
    {
        if (!$this->table) {
            throw new \Exception;
        }

        if (!is_array($this->table)) {
            $this->table = [$this->table];
        }

        return implode(',', $this->table);
    }

    /**
     * @return string
     */
    private function buildSelectJoin()
    {
        if (!$this->joins) {
            return '';
        }

        return implode(' ', $this->joins);
    }

    /**
     * @return string
     */
    private function buildSelectWhere()
    {
        if (!$this->wheres) {
            return '';
        }

        $where_query = ' WHERE ';

        return $where_query . implode(' AND ', $this->wheres);
    }


    /**
     * @return string
     */
    private function buildSelectGroup()
    {
        if (!$this->groups) {
            return '';
        }

        $group_query = ' GROUP BY ';

        return $group_query . implode(',', $this->groups);
    }

    /**
     * @return string
     */
    private function buildSelectOrder()
    {
        if (!$this->orders) {
            return '';
        }

        $order_query = ' ORDER ';

        return $order_query . implode(',', $this->orders);
    }

    /**
     * @return string
     */
    private function buildSelectLimit()
    {
        if (isset($this->limit) && isset($this->offset)) {
            return " LIMIT " . $this->offset . ',' . $this->limit;
        }

        if (isset($this->limit)) {
            return " LIMIT " . $this->limit;
        }

        if (isset($this->offset)) {
            return " LIMIT " . $this->offset;
        }

        return '';
    }

    /**
     * @param $values
     * @return mixed|string
     */
    public function buildInsertQuery($values)
    {
        $sql = "INSERT INTO {TABLE} ({COLUMNS}) VALUES ({VALUES});";

        $cols = [];
        $vals = [];
        foreach ($values as $k => $v) {
            $cols[] = $k;
            $vals[] = $v;
        }

        $columns = implode(',', $cols);
        $spaces  = $this->buildPrepareColumns($vals);

        $this->addValues($values);

        $sql = str_replace("{TABLE}",   $this->table, $sql);
        $sql = str_replace("{COLUMNS}", $columns,     $sql);
        $sql = str_replace("{VALUES}",  $spaces,      $sql);

        return $sql;
    }

    /**
     * @param $set_values
     * @return bool|mixed|string
     */
    public function buildUpdateQuery($set_values)
    {
        $sql = "UPDATE {TABLE} SET {COLUMNS} {WHERE};";

        $where = $this->buildSelectWhere();

        // WHEREなしは安全のため不可
        if (!$where) {
            return false;
        }

        $cols = [];
        $vals = [];
        foreach ($set_values as $k => $v) {
            $cols[] = $this->table . '.' . $k .' = ? ';
            $vals[] = $v;
        }

        $columns = implode(',', $cols);

        $oldValues = $this->getValues();

        $this->resetValues($vals); // SET value is before WHERE.
        $this->addValues($oldValues);

        $sql = str_replace("{TABLE}",   $this->table, $sql);
        $sql = str_replace("{COLUMNS}", $columns, $sql);

        $sql = str_replace("{WHERE}",   $this->buildSelectWhere(), $sql);

        return $sql;
    }

    /**
     * @return bool|mixed|string
     */
    public function buildDeleteQuery()
    {
        $sql = "DELETE FROM {TABLE} {WHERE};";

        $where = $this->buildSelectWhere();

        // warning : empty where query
        if (!$where) {
            return false;
        }

        $sql = str_replace("{TABLE}", $this->table, $sql);
        $sql = str_replace("{WHERE}", $this->buildSelectWhere(), $sql);

        return $sql;
    }

    /**
     * @param $values
     * @return string
     */
    private function buildPrepareColumns($values) {
        return rtrim(str_repeat('?,', count(array_keys($values))), ',');
    }
}