<?php

namespace Phacil\Integration\Traits;

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
