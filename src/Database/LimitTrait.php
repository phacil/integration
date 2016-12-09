<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Component\Integration\Database;

/**
 * Description of LimitTrait
 *
 * @author alisson
 */
trait LimitTrait {
    public function limit($limit){
        $this->limit = $limit;
        return $this;
    }
    
    public function offset($offset){
        $this->offset = $offset;
        return $this;
    }
    
    
}
