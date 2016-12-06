<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Component\Integration\ORM;

use Phacil\Component\Integration\Integration;
use Phacil\Component\Integration\Database\Query;

/**
 * Description of Model
 *
 * @author alisson
 */
class Model {
    private $associations;
    private $table_name;
    private $table_alias;
    private $primary_key;
    private $hooks;

    function __construct()
    {
        $this->init();
    }

    function init() {
        
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
            $generated_table_name = strtolower(get_class($this));
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
    
    public function find($options = [])
    {
        $table = $this->table_name();
        return ORMQuery::$table();
    }
/**/
    /*! Associations */
    function associations()
    {
        return $this->associations;
    }
    
    function belongs_to($name, $options = null)
    {
        $this->associations[] = array(
            'name' => $name,
            'type' => 'belongs_to',
            'options' => $options
        );
    }

    function has_many($name, $options = null)
    {
            $this->associations[] = array(
                    'name' => $name,
                    'type' => 'has_many',
                    'options' => $options
            );
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

    /*! CRUD Functions */
    public function create($data)
    {
            //call before_create and before_save
            //$data = $this->run_hook('before_create', $data);
            $data = $this->run_hook('before_save', null, $data);

            $clean_data = [];

            foreach ($data as $key => $value)
            {
                    if (get_class($value) == 'Row')
                    {
                            $primary_key = $value->model->primary_key();
                            $id = $value->$primary_key;

                            $clean_data[$primary_key] = $id;
                    }
                    else
                    {
                            $clean_data[$key] = $value;
                    }
            }
            $data = $clean_data;

            $db = Integration::Instance();
            $db->create($this->table_name(), $data);
            $id = $db->db->insert_id;

            $obj = $this->find($id);

            //call after_create and after_save
            //$obj = $this->run_hook('after_create', $obj);
            $obj = $this->run_hook('after_save', $obj, $data);

            return $obj;
    }
    public function update($id, $data)
    {
            $row = $this->find($id);
            $row->update($data);
    }
    public function destroy($id)
    {
            $row = $this->find($id);
            $row->destory();
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
