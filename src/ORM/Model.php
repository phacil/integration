<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Integration\ORM;

use Phacil\Integration\Integration;

/**
 * Description of Model
 *
 * @author alisson
 */
class Model {
    
    use TableTrait;
    
    private $associations;
    public  $table_name;
    private $table_alias;
    public  $primary_key;
    private $hooks;
    private $prepareAssociation;

    function __construct()
    {

    }

    function primary_key($primary_key = null)
    {
        if (empty($this->primary_key) || $primary_key)
        {
            $this->primary_key = $primary_key ? $primary_key : 'id';
        }

        return $this->primary_key;
    }

    function table_name($table_name = null)
    {
        if (empty($this->table_name) || $table_name)
        {
            $table = explode('\\', strtolower(get_class($this)));
            $generated_table_name = end($table);
            $this->table_name = $table_name ? $table_name : $generated_table_name;
        }

        return $this->table_name;
    }

    function table_alias($table_alias = null)
    {
        if ($table_alias)
        {
            $this->table_alias = $table_alias;
        }

        return $this->table_alias;
    }

    function get($justAlias = false)
    {
        if ($justAlias)
        {
            return $this->table_alias() ? $this->table_alias() : $this->table_name();
        }
        return $this->table_name() . ($this->table_alias() ? ' ' . $this->table_alias() : '');
    }
    
    public function find($id = null)
    {
        $table = $this->table_name();
        
        $query = ORMQuery::$table();
        
        if($id){
            $query->where([$table . '.' . $this->primary_key()=>$id]);
        }
                
        foreach($this->associations as $assoc){
            
            if($assoc['type'] == 'belongs_to'){
                
                $_table_name = $this->assoc_table_name(ORMQuery::$baseNamespace, $assoc);
                
                $query->leftJoin(   $_table_name, 
                                $_table_name . '.' . $this->primary_key(),
                                $this->table_name() . '.'. $assoc['options']['foreign_key']
                            );
            }else if($assoc['type'] == 'has_many'){
                $query->children[] = $assoc;
            }
        }
        
        return $query;
    }

    /*! Associations */
    function getAssociations()
    {
        return $this->associations;
    }

    function cleanAssociations()
    {
        $this->associations = [];
        return $this;
    }
    
    function table($name, $options = null)
    {
        $this->associations[] = array(
            'name' => $name,
            'type' => $this->prepareAssociation,
            'to'=>'table',
            'options' => $options
        );
        return $this;
    }
    
    function model($name, $options = null)
    {
        $this->associations[] = array(
            'name' => $name,
            'type' => $this->prepareAssociation,
            'to'=>'model',
            'options' => $options
        );
        return $this;
    }
    
    function belongs_to()
    {
        $this->prepareAssociation = 'belongs_to';
        return $this;
    }

    function has_many()
    {
        $this->prepareAssociation = 'has_many';
        return $this;
    }

    function association($name)
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
    
    public function insert(Array $data)
    {
        //call before_create and before_save
        //$data = $this->run_hook('before_save', null, $data);

        $table = $this->table_name();
        
        $query = ORMQuery::$table();
        
        $insertId = $query->insert($data);
                
        $obj = $this->find()
                    ->where([$this->table_name().'.'.$this->primary_key()=>$insertId])
                    ->get(1);

        //call after_create and after_save
        //$obj = $this->run_hook('after_create', $obj);
        //$obj = $this->run_hook('after_save', $obj, $data);

        return $obj;
    }
    
/* da qui pra cima funcionando */    
    
    /*! Hooks */
    function before_save($name)
    {
            $this->hooks['before_save'][] = $name;
    }
    function after_save($name)
    {
            $this->hooks['after_save'][] = $name;
    }
    function before_update($name)
    {
            $this->hooks['before_update'][] = $name;
    }
    function after_update($name)
    {
            $this->hooks['after_update'][] = $name;
    }
    function run_hook($hook, $row = null, $data = null)
    {
            if (!empty($this->hooks[$hook]))
            {
                    foreach ($this->hooks[$hook] as $h)
                    {
                            $data = $this->$h($row, $data);
                    }
            }
            return in_array($hook, array('after_save', 'after_create', 'after_update')) ? $row : $data;
    }

    public function first($options = [])
    {
            $options = array_merge(array(
                    'limit' => 1,
                    'order' => $this->primary_key().' ASC'
            ), $options);

            $results = $this->find($options);
            if (count($results))
            {
                    return $results[0];
            }
            return null;
    }
    public function earliest() { return $this->last(); }

    public function last($options = [])
    {
            $options = array_merge(array(
                    'limit' => 1,
                    'order' => $this->primary_key() . ' DESC'
            ), $options);

            $results = $this->find($options);
            if (count($results))
            {
                    return $results[0];
            }
            return null;
    }
    public function latest() { return $this->last(); }

    public function count($options = [])
    {
            $options['select'] = 'COUNT(' . $this->get(true) . '.' . $this->primary_key() . ') AS count';
            $results = $this->find($options);
            $row = $results[0];
            $count = $row->count;
            return $count;
    }

    /*function __call($method, $arguments)
    {

    }*/
}
