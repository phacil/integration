<?php

namespace Phacil\Integration\Database;

trait HooksTrait {
    
    public $hooks = null;
    
    public function init_hooks(){
        $this->hooks = new \stdClass();
        
        $this->hooks->before_select = function($query){return $query;}; 
        $this->hooks->before_find = function($query){return $query;};
        $this->hooks->before_insert = function($data){return $data;};
        $this->hooks->before_update = function($data){return $data;};
        
        $this->hooks->after_select = function($result){return $result;};
        $this->hooks->after_find = function($result){return $result;};
        $this->hooks->after_insert = function($inserted_id){return $inserted_id;};
        $this->hooks->after_select = function($updated_id){return $updated_id;};
    }
       
    public function beforeSelect($query)
    {
        if(isset($this->hooks->before_select)){
            $query = call_user_func($this->hooks->before_select, $query);
        }
        return $query;
    }
    
    public function afterSelect($result)
    {
        if(isset($this->hooks->after_select)){
            $result = call_user_func($this->hooks->after_select, $result);
        }
        return $result;
    }
    
    public function beforeFind($query)
    {
        if(isset($this->hooks->before_find)){
            $query = call_user_func($this->hooks->before_find, $query);
        }
        return $query;
    }
    
    public function afterFind($result)
    {
        if(isset($this->hooks->after_find)){
            $result = call_user_func($this->hooks->after_find, $result);
        }
        return $result;
    }
    
    public function beforeInsert($data)
    {
        if(isset($this->hooks->before_insert)){
            $data = call_user_func($this->hooks->before_insert, $data);
        }
        return $data;
    }
    
    public function afterInsert($inserted_id)
    {
        if(isset($this->hooks->after_insert)){
            $inserted_data = call_user_func($this->hooks->after_insert, $inserted_id);
        }
        return $inserted_id;
    }
    
    public function beforeUpdate($data)
    {
        if(isset($this->hooks->before_update)){
            $data = call_user_func($this->hooks->before_update, $data);
        }
        return $data;
    }
    
    public function afterUpdate($updated_id)
    {
        if(isset($this->hooks->after_update)){
            $updated_data = call_user_func($this->hooks->after_update, $updated_id);
        }
        return $updated_id;
    }
    
    /* TODO */
    
    public function beforeDelete()
    {
        
    }
    
    public function afterDelete()
    {
        
    }
}
