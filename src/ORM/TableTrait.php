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
   
    private function assoc_table_name($base_namespace, $assoc){
       
        if($assoc['to'] == 'table'){
            return strtolower($assoc['name']);
        }        
        
        //Mudar para Inflector
        $_class = ucwords($assoc['name'], ' \\');
        
        if(strpos($assoc['name'], '\\') === false)            
        {            
            $_class .= '\\' . $_class;
        }
        
        $class = $base_namespace . $_class;
        
        $parentObject = new $class();
            
        return $parentObject->table_name();        
    }
    
}
