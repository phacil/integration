<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Component\Integration\ORM;

use Phacil\Component\Integration\Database\Query;
use Phacil\Component\Integration\Integration;

/**
 * Description of ORMQuery
 *
 * @author alisson
 */

class ORMQuery extends Query implements \IteratorAggregate{
    
    use TableTrait;
    
    private $model;
    public $children = [];
    public static $baseNamespace = "\\";
    
    protected function injectRow($data){        
        return new ORMRow($data, $this->model);
    }
    
    private function isClass($assoc_name){
        if(strpos($assoc_name, '\\') !== false){
            return true;
        }
        return false;
    }

    public function getAll($array = false, $all = false, $reset = true){
        
        $query = $this->buildQuery();
      
        $result = $this->query($query, $all, $array, $reset);
	$collection = [];
                
        foreach($result as $data){
            $_data = (array) $data;
            foreach($this->children as $child){

                $_table_name = $this->assoc_table_name(ORMQuery::$baseNamespace, $child);
                //print_r($_table_name);

                if($this->isClass($child['name'])){
                    $_class = ucwords($child['name'], ' \\');
                    $class = self::$baseNamespace . $_class;
                    $childObject = new $class();
                    $query = $childObject
                            ->cleanAssociations()
                            ->find();
                }else{
                    $query = self::__callStatic($_table_name,[]);                        
                }
                
                if(isset($child['options']['by'])){
                    $n_m = $child['options']['by'];
                    $query->select($_table_name.'.*');
                    $query->leftJoin($n_m, $n_m.'.'.$_table_name . '_id', $_table_name.'.id')
                            ->where([$n_m . '.' . $child['options']['foreign_key']=>
                            $_data[$this->model.'.id']]); 
                }else{
                    $query->where([$_table_name . '.' . $child['options']['foreign_key']=>
                            $_data[$this->model.'.id']]);        
                }
                $data->{$_table_name} = $query->get();
            }
           $collection[] = $this->injectRow($data);
        }
        return $collection;
    }
         
    public static function __callStatic($name, $arguments) {
        
        $pdo = Integration::getConfig(Integration::getActualConfig());
        $connection = new self($pdo);
               
        if(method_exists($connection, $name)){
            return call_user_func_array(array($connection, $name), $arguments);
        }else{
            $connection->model = $name;
            $connection2 = call_user_func_array(array($connection,'from'), (array) $name);
            if(empty($arguments)){
                return $connection2;
            }else{
                return call_user_func_array(array($connection2,'where'), $arguments);
            }
        }
    }
    
}
