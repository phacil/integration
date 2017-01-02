<?php

namespace Phacil\Integration\ORM;

trait AssociationTrait {
   
    private function assoc_table_name($base_namespace, $assoc){
              
        if($assoc['to'] == 'table'){
            $table = strtolower($assoc['name']);
            $table .= !is_null($assoc['alias'])?' as '. $assoc['alias']:null;
            return $table;
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
    
    private function assoc_alias($base_namespace, $assoc){
        
        //pr($assoc); exit;
        if($assoc['to'] == 'table'){
            $alias = !is_null($assoc['alias'])?$assoc['alias']:$assoc['name'];
            return $alias;
        }
        
        //TODO for model
    }
    
    public function getAssociations()
    {
        return $this->associations;
    }

    public function cleanAssociations()
    {
        $this->associations = [];
        return $this;
    }
    
    public function table($name, $options = null)
    {
        $this->associations[] = [
            'name' => $name,
            'alias' => (!is_null($this->prepareAssociation[1]))?$this->prepareAssociation[1]:null,
            'type' => $this->prepareAssociation[0],
            'to'=>'table',
            'options' => $options
        ];
        return $this;
    }
    
    public function model($name, $options = null)
    {
        $this->associations[] = array(
            'name' => $name,
            'alias' => (!is_null($this->prepareAssociation[1]))?$this->prepareAssociation[1]:null,
            'type' => $this->prepareAssociation,
            'to'=>'model',
            'options' => $options
        );
        return $this;
    }
    
    public function belongs_to($alias = null)
    {
        $this->prepareAssociation = ['belongs_to', $alias];
        return $this;
    }

    public function has_many($alias = null)
    {
        $this->prepareAssociation = ['has_many', $alias];
        return $this;
    }

    public function association($name)
    {
        foreach ($this->associations() as $association)
        {
            if ($association['name'] == $name)
            {
                return $association;
            }
        }
        return null;
    }
    
    public function hooks(){
        return $this->hooks;
    }
    
}
