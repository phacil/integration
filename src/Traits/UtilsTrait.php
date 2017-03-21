<?php

namespace Phacil\Integration\Traits;

trait UtilsTrait {
    
    public function escape($data) {
        return $this->pdo->quote(trim($data));
    }
    
    public function isNotNullReturn($var, $prefix = null, $sulfix = null){
        if (!is_null($var)){
            return $prefix . $var . $sulfix;
        }        
        return null;
    }
}
