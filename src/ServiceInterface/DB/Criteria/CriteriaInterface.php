<?php

namespace ServiceInterface\DB\Criteria;

interface CriteriaInterface 
{
    public function join($table, $column, $table2, $column2, $op = '=');
    public function where($table, $column, $values = [], $type = '=');
    public function whereRaw($query, $values = []);
    public function group($table, $column);
    public function order($table, $column, $type);
    public function limit($limit, $offset = 0);
    
    public function buildSelectQuery();
    public function buildInsertQuery($values);
    public function buildUpdateQuery($set_values);
    public function buildDeleteQuery();
}
