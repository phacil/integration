<?php

namespace Phacil\Integration\Traits;
use Phacil\Integration\Database\Raw;

trait SelectTrait {
    
    public $select 	= ['*'];
    public $from 	= null;
    
    public function select($select=['*']) {
        
        if($select instanceof Raw){
           $selects = [$select];
        }else if(!is_array($select)){
            $selects = explode(',', $select);
        }else{
            $selects = $select;
        }
        $this->select = $selects;
        
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
