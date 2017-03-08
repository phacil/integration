<?php

namespace Phacil\Integration\ORM;

class Model {
    
    use AssociationTrait;
    
    private $associations;
    public  $table_name;
    private $table_alias;
    public  $primary_key;
    private $hooks;
    private $prepareAssociation;
    private $validate;

    public function __construct()
    {
        $this->hooks = new \stdClass();
        $this->validate = new \stdClass();
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

    public function getInstanceQuery()
    {
        $table = $this->table_name();        
        return ORMQuery::$table()->setHooks($this->hooks)->setValidation($this->validate);
    }
    
    public function find($id = null)
    {       
        $query = $this->getInstanceQuery();
        
        if($id){
            $query->where([$this->table_name() . '.' . $this->primary_key()=>$id]);
        }
                
        foreach($this->associations as $assoc){
            
            if($assoc['type'] == 'belongs_to'){
                
                $_table_name = $this->assoc_table_name(ORMQuery::$baseNamespace, $assoc);
                $_alias = $this->assoc_alias(ORMQuery::$baseNamespace, $assoc);
                
                $query->leftJoin($_table_name, 
                                $_alias . '.' . $this->primary_key(),
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
    
}
