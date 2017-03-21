<?php

namespace Phacil\Integration\Traits;

trait GroupTrait {
    
    public $groupBy 	= null;
    public $having 	= null;
    
    public function groupBy($groupBy) {
        if(is_array($groupBy)){
            $this->groupBy = implode(', ', $groupBy);
        }else{
            $this->groupBy = $groupBy;
        }

        return $this;
    }
	
    public function having($field, $op = null, $val = null){
        if(is_array($op)){
            $x = explode('?', $field);
            $w = '';

            foreach($x as $k => $v){
                if(!empty($v)){
                    $w .= $v . (isset($op[$k]) ? $this->escape($op[$k]) : '');
                }
            }

            $this->having = $w;
        }

        elseif (!in_array($op, $this->op)){
            $this->having = $field . ' > ' . $this->escape($op);
        }else{
            $this->having = $field . ' ' . $op . ' ' . $this->escape($val);
        }

        return $this;
    }
}
