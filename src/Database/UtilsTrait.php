<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Integration\Database;

/**
 * Description of PDOUtilsTrait
 *
 * @author alisson
 */
trait UtilsTrait {
    public function escape($data) {
        return $this->pdo->quote(trim($data));
    }
    
    private function isNotNullReturn($var, $prefix = null, $sulfix = null){
        if (!is_null($var)){
            return $prefix . $var . $sulfix;
        }        
        return null;
    }
}
