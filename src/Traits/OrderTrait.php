<?php

namespace Phacil\Integration\Traits;

trait OrderTrait {
    
    protected $orderBy 	= null;
    
    public function orderBy($orderBy, $order_dir = null){
        
        if(is_null($orderBy) || empty($orderBy)){
            return $this;
        }
        
        if (!is_null($order_dir)){
            $this->orderBy = $orderBy . ' ' . strtoupper($order_dir);
        }
        else{
            if(stristr($orderBy, ' ') || $orderBy == 'rand()'){
                $this->orderBy = $orderBy;
            }else{
                $this->orderBy = $orderBy . ' ASC';
            }
        }

        return $this;
    }
}
