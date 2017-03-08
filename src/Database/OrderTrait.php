<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Integration\Database;

/**
 * Description of OrderTrait
 *
 * @author alisson
 */
trait OrderTrait {
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
