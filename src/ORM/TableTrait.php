<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Component\Integration\ORM;

/**
 * Description of TableTrait
 *
 * @author alisson
 */
trait TableTrait {
   
    private function assoc_table_name($base_namespace, $class_name){
        if(strpos($class_name, '\\') !== false){
            $_class = ucwords($class_name, ' \\');
            $class = $base_namespace . $_class;

            //$childObject = (new $class);
            $parentObject = new $class();
            
            return $parentObject->table_name();
        }else{
            return $class_name;
        }        
    }
    
}
