<?php

namespace Phacil\Integration\Database;

use Phacil\Integration\ORM\Validator;

trait PersistentTrait {
  
    public function insertId()
    {
        return $this->insertId;
    }
    
    private function doValidate($data, $rules)
    {
        $validation_result = Validator::validate($data, $rules);
        if (!$validation_result->isSuccess()) {
            $this->validate()->errors = $validation_result->getErrors();
            return false;
        }
        $this->validate()->isSucces = true;
        return true;
    }        

    public function insert(Array $data = [])
    {        
        $data = $this->beforeInsert($data);
       
        if(!$this->doValidate($data, $this->validate)){
            return false;
        }
        
        $columns = array_keys($data);
        $column = implode(',', $columns);
        $val = implode(', ', array_map([$this, 'escape'], $data));

        $query = 'INSERT INTO ' . $this->from . ' (' . $column . ') VALUES (' . $val . ')';
        $query = $this->query($query);

        if ($query){
            $this->insertId = $this->pdo->lastInsertId();
            return $this->afterInsert($this->insertId());
        }
        
        return false;
    }

    public function update(Array $data)
    {
        $data = $this->beforeUpdate($data);
        
        if(!$this->doValidate($data, $this->validate)){
            return false;
        }
        
        $query = 'UPDATE ' . $this->from . ' SET ';
        $values = [];

        foreach ($data as $column => $val){
            $values[] = $column . '=' . $this->escape($val);
        }

        $query .= (is_array($data) ? implode(',', $values) : $data);
        
        if(isset($this->where[$this->model.'.id'])){
            $updated_id = $this->where[$this->model.'.id'];
        }; 

        if (!is_null($this->where)) { $query .= ' WHERE ' . $this->where;}

        if (!is_null($this->orderBy)) {$query .= ' ORDER BY ' . $this->orderBy;}

        if (!is_null($this->limit)) {$query .= ' LIMIT ' . $this->limit;}        
               
        $query = $this->query($query);
        
        if ($query){
            if(isset($updated_id)){
                return $this->afterUpdate($updated_id);
            }
            return true;
        }
        
        return false;
    }

    public function delete()
    {
        $query = 'DELETE FROM ' . $this->from;

        if (!is_null($this->where)){
            $query .= ' WHERE ' . $this->where;

            if (!is_null($this->orderBy)){
                $query .= ' ORDER BY ' . $this->orderBy;
            }

            if (!is_null($this->limit)){
                $query .= ' LIMIT ' . $this->limit;
            }
        }else{
            //TODO Criar uma funcao truncate
            //$query = 'TRUNCATE TABLE ' . $this->from;
        }

        return $this->query($query);
    }
}
