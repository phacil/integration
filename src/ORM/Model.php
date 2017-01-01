<?php

namespace Phacil\Integration\ORM;

class Model {
    
    use TableTrait;
    
    private $associations;
    public  $table_name;
    private $table_alias;
    public  $primary_key;
    private $hooks;
    private $prepareAssociation;

    public function __construct()
    {
        $this->hooks = new \stdClass();
    }
    
    public function primary_key($primary_key = null)
    {
        if (empty($this->primary_key) || $primary_key)
        {
            $this->primary_key = $primary_key ? $primary_key : 'id';
        }

        return $this->primary_key;
    }

    public function table_name($table_name = null)
    {
        if (empty($this->table_name) || $table_name)
        {
            $table = explode('\\', strtolower(get_class($this)));
            $generated_table_name = end($table);
            $this->table_name = $table_name ? $table_name : $generated_table_name;
        }

        return $this->table_name;
    }

    public function table_alias($table_alias = null)
    {
        if ($table_alias)
        {
            $this->table_alias = $table_alias;
        }

        return $this->table_alias;
    }

    public function get($justAlias = false)
    {
        if ($justAlias)
        {
            return $this->table_alias() ? $this->table_alias() : $this->table_name();
        }
        return $this->table_name() . ($this->table_alias() ? ' ' . $this->table_alias() : '');
    }
    

    /*! Associations */
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
        $this->associations[] = array(
            'name' => $name,
            'type' => $this->prepareAssociation,
            'to'=>'table',
            'options' => $options
        );
        return $this;
    }
    
    public function model($name, $options = null)
    {
        $this->associations[] = array(
            'name' => $name,
            'type' => $this->prepareAssociation,
            'to'=>'model',
            'options' => $options
        );
        return $this;
    }
    
    public function belongs_to()
    {
        $this->prepareAssociation = 'belongs_to';
        return $this;
    }

    public function has_many()
    {
        $this->prepareAssociation = 'has_many';
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
    
    public function getInstanceQuery()
    {
        $table = $this->table_name();        
        return ORMQuery::$table()->setHooks($this->hooks);        
    }
    
    public function find($id = null)
    {       
        $query = $this->getInstanceQuery();
        
        if($id){
            $query->where([$table . '.' . $this->primary_key()=>$id]);
        }
                
        foreach($this->associations as $assoc){
            
            if($assoc['type'] == 'belongs_to'){
                
                $_table_name = $this->assoc_table_name(ORMQuery::$baseNamespace, $assoc);
                
                $query->leftJoin($_table_name, 
                                $_table_name . '.' . $this->primary_key(),
                                $this->table_name() . '.'. $assoc['options']['foreign_key']
                            );
            }else if($assoc['type'] == 'has_many'){
                $query->children[] = $assoc;
            }
        }
        
        return $query;
    }
    
    public function insert(Array $data)
    {
        //call before_create and before_save
        $query = $this->getInstanceQuery();
        
        return $query->insert($data);        
    }
    
    /*! Hooks */        
    public function hooks(){
        return $this->hooks;
    }
}
