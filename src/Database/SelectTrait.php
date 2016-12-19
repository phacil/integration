<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Integration\Database;

/**
 * Description of SelectTrait
 *
 * @author alisson
 */
trait SelectTrait {
    public function select($select='*') {
        if(is_array($select)){
            $this->select = implode(', ', $select);
        }else{
            $this->select = $select;
        }
        return $this;
    }
    
    public function from($from){
        
        if(is_array($from)){
            $f = '';
            foreach($from as $key){
                $f .= $this->prefix . $key . ', ';
            }

            $this->from = rtrim($f, ', ');
        }else{
                $this->from = $this->prefix . $from;
        }

        return $this;
    }
}
