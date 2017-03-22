<?php

namespace Phacil\Integration\ORM;

use Phacil\Integration\Database\Query;
use Phacil\Integration\Integration;

class ORMQuery extends Query{
    
    use \Phacil\Integration\Traits\AssociationTrait;
    
    public $model;
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
    
    public function getModel(){
        return $this->model;
    }

    public function getAll($array = false, $all = false, $reset = true){
                
        $query = $this->beforeFind($this)->buildQuery();
        
        $result = $this->query($query, $all, $array, $reset);
                
	$collection = [];
        
        if(!is_array($result)){
            return $this->injectRow($result);
        }
                
        foreach($result as $data){
            $_data = (array) $data;
            foreach($this->children as $child){

                $_table_name = $this->assoc_table_name(ORMQuery::$baseNamespace, $child);
                //print_r($child);

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
                    $query->where([$child['alias'] . '.' . $child['options']['foreign_key']=>
                            $_data[$this->model.'.id']]);        
                }
                $data->{$_table_name} = $query->get();
            }
            //pr($data);exit;
           $collection[] = $this->injectRow($data);
        }
        
        return $this->afterFind($collection);
    }
         
    public static function __callStatic($name, $arguments) {
        
        $pdo = Integration::exec(Integration::getActualConfig());
        $connection = new self($pdo);
        
        if(method_exists($connection, $name)){
            $query =  call_user_func_array(array($connection, $name), $arguments);
        }else{
            $connection->model = $name;
            $query = call_user_func_array(array($connection,'from'), (array) $name);
            
            if(!empty($arguments)){
                $query = call_user_func_array(array($query,'where'), $arguments);
            }
            
        }
                
        return $query;
    }
    
    /* CRUD */    
    
    public function setHooks($hooks){
        $this->hooks = $hooks;
        return $this;
    }
    
    public function setValidation($validate){
        $this->validate = $validate;
        return $this;
    }
    
/*    public function insert(Array $data) {
        
        //before update
        if(isset($this->hooks->before_insert)){
            $data = call_user_func($this->hooks->before_insert, $data);
        }
        
        $saved_data = parent::insert($data);
        
        //after insert
        if(isset($this->hooks->before_insert)){
            $saved_data = call_user_func($this->hooks->before_insert, $saved_data);
        }
        
        return $saved_data;
        
    }
    
    
    public function update(Array $data) {
        
        //before update
        if(isset($this->hooks->before_update)){
            $data = call_user_func($this->hooks->before_update, $data);
        }
        
        $saved_data = parent::update($data);
        
        if(isset($this->hooks->after_update)){
            $saved_data = call_user_func($this->hooks->after_update, $saved_data);
        }
        
        return $saved_data;
        
    }
    
    public function delete() {
        
        if(isset($this->hooks->before_delete)){
            call_user_func($this->hooks->before_delete);
        }
        
        $deleted_data = parent::delete();
        
        if(isset($this->hooks->after_delete)){
            $deleted_data = call_user_func($this->hooks->after_delete, $deleted_data);
        }
        
        return $deleted_data;
        
    }
    
 * 
 * */

}
