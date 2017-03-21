<?php

namespace Phacil\Integration\Traits;

trait WhereTrait {
    
    protected $where 	= null;
    
    public function where($where, $op = null, $val = null, $ao = 'AND'){
        if (is_array($where)){
            $_where = [];
            
            foreach ($where as $column => $data) {
                $_where[] = $column . '=' . $this->escape($data);
            }

            $where = implode(' '.$ao.' ', $_where);
        }else{
            
            if(is_null($op) && is_null($val)){
                
            }else if(is_array($op)){
                $x = explode('?', $where);
                $w = '';

                foreach($x as $k => $v){
                    if(!empty($v)){
                        $w .= $v . (isset($op[$k]) ? $this->escape($op[$k]) : '');
                    }
                }

                $where = $w;
            }elseif (!in_array($op, $this->op)){
                $where = $where . ' = ' . $this->escape($op);
            }else{
                $where = $where . ' ' . $op . ' ' . $this->escape($val);
            }
        }

        if($this->grouped){
            $where = '(' . $where;
            $this->grouped = false;
        }

        if (is_null($this->where)){
            $this->where = $where;
        }else{
            $this->where = $this->where . ' '.$ao.' ' . $where;
        }

        return $this;
    }

    public function orWhere($where, $op=null, $val=null){
        $this->where($where, $op, $val, 'OR');
        return $this;
    }

    public function grouped(\Closure $obj){
        $this->grouped = true;
        call_user_func($obj);
        $this->where .= ')';

        return $this;
    }

    public function in($field, Array $keys, $not = '', $ao = 'AND'){
        if (is_array($keys)){
            $_keys = [];

            foreach ($keys as $k => $v){
                $_keys[] = (is_numeric($v) ? $v : $this->escape($v));
            }

            $keys = implode(', ', $_keys);

            if (is_null($this->where)){
                $this->where = $field . ' ' . $not . 'IN (' . $keys . ')';
            }else{
                $this->where = $this->where . ' ' . $ao . ' ' . $field . ' '.$not.'IN (' . $keys . ')';
            }
        }

        return $this;
    }

    public function notIn($field, Array $keys){
        $this->in($field, $keys, 'NOT ', 'AND');
        return $this;
    }

    public function orIn($field, Array $keys){
        $this->in($field, $keys, '', 'OR');
        return $this;
    }

    public function orNotIn($field, Array $keys){
        $this->in($field, $keys, 'NOT ', 'OR');
        return $this;
    }

    public function between($field, $value1, $value2, $not = '', $ao = 'AND') {
        if (is_null($this->where)){ 
            $this->where = $field . ' ' . $not . 'BETWEEN ' . $this->escape($value1) . ' AND ' . $this->escape($value2);
        }else{
            $this->where = $this->where . ' ' . $ao . ' ' . $field . ' ' . $not . 'BETWEEN ' . $this->escape($value1) . ' AND ' . $this->escape($value2);
        }
        return $this;
    }
	
    public function notBetween($field, $value1, $value2){
        $this->between($field, $value1, $value2, 'NOT ', 'AND');
        return $this;
    }

    public function orBetween($field, $value1, $value2){
        $this->between($field, $value1, $value2, '', 'OR');
        return $this;
    }

    public function orNotBetween($field, $value1, $value2){
        $this->between($field, $value1, $value2, 'NOT ', 'OR');
        return $this;
    }
	
    public function like($field, $data, $type = '%-%', $ao = 'AND'){
        $like = '';

        if($type == '-%'){
            $like = $data.'%';
        }else if($type == '%-'){
            $like = '%'.$data;
        }else{
            $like = '%'.$data.'%';
        }

        $like = $this->escape($like);

        if (is_null($this->where)){
            $this->where = $field . ' LIKE ' . $like;
        }else{
            $this->where = $this->where . ' '.$ao.' ' . $field . ' LIKE ' . $like;
        }

        return $this;
    }
	
    public function orLike($field, $data, $type = '%-%'){
        $this->like($field, $data, $type, 'OR');
        return $this;
    }
}
