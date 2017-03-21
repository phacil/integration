<?php

namespace Phacil\Integration\Traits;

trait JoinTrait{
    
    public $join 	= null;
    
    
    public function join($table, $field1, $op = null, $field2 = null, $join = '') {
        if(is_array($table)){
            $q = '';

            if(count($table) > 3){
                $q .= strtoupper($table[0]) . ' JOIN ' . $table[1] . ' ON ' . $table[2] . ' = ' . $table[3];
            }else{
                $q .= strtoupper($table[0]) . ' JOIN ' . $table[1] . ' ON ' . $table[2];
            }
            
            if (is_null($this->join)){
                $this->join = ' ' . $q;
            }else{
                $this->join = $this->join . ' ' . $q;
            }
        }else{
            $where = $field1;
            $table = $this->prefix . $table;

            if(!is_null($op)) {
                $where = (!in_array($op, $this->op) ? $this->prefix . $field1 . ' = ' . $this->prefix . $op : $this->prefix . $field1 . ' ' . $op . ' ' . $this->prefix . $field2);
            }
            if (is_null($this->join)){
                $this->join = ' ' . $join . 'JOIN' . ' ' . $table . ' ON ' . $where;
            }else{
                $this->join = $this->join . ' ' . $join . 'JOIN' . ' ' . $table . ' ON ' . $where;
            }
        }

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
