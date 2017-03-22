<?php

namespace Phacil\Integration\Traits;

trait JoinTrait{
    
    public $join = [];
    public $op 	= array('=','!=','<','>','<=','>=','<>');
    
    public function join($table, $field1, $op = null, $field2 = null, $join = '') {
        //eliminando o array $table
        $this->join[$table]['table'] = $table;
        $this->join[$table]['field1'] = $field1;
        $this->join[$table]['op'] = (is_null($field2) || empty($field2))?'=':$op;
        $this->join[$table]['field2'] = (is_null($field2) || empty($field2))?$op:$field2;
        $this->join[$table]['join'] = $join;
                
        return $this;
    }
    
    public function innerJoin($table, $field1, $op = '', $field2 = '')
    {
        $this->join($table, $field1, $op, $field2, 'INNER ');
        return $this;
    }

    public function leftJoin($table, $field1, $op = '', $field2 = '') 
    {
        $this->join($table, $field1, $op, $field2, 'LEFT ');
        return $this;
    }

    public function rightJoin($table, $field1, $op = '', $field2 = '')
    {
        $this->join($table, $field1, $op, $field2, 'RIGHT ');
        return $this;
    }
}
