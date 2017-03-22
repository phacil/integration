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
            $this->from = $from;
        }else{
            $this->from = explode(',', $from);
        }
        return $this;
    }
    
}
