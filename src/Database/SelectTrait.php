<?php

namespace Phacil\Integration\Database;

trait SelectTrait {
    
    protected $select 	= '*';
    protected $from 	= null;
    
    public function select($select='*') {
        if(is_array($select)){
            $this->select = implode(', ', $select);
        }else{
            $this->select = $select;
        }
        return $this;
    }
    
    public function from($from){
        
        if(is_array($from)){
            $f = '';
            foreach($from as $key => $value){
                if(is_int($key)){
                   $value = $key . ' as ' . $value;   
                }
                $f .= $this->prefix . $value . ', ';
            }
            $this->from = rtrim($f, ', ');
        }else{
            $this->from = $this->prefix . $from;
        }

        return $this;
    }
}
