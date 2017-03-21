<?php

namespace Phacil\Integration\Traits;

trait LimitTrait {
    
    protected $limit 	= null;
    protected $offset 	= null;
    
    public function limit($limit){
        $this->limit = $limit;
        return $this;
    }
    
    public function offset($offset){
        $this->offset = $offset;
        return $this;
    }
    
}
