<?php

namespace Phacil\Integration\Database;

trait PersistentTrait {
        
    public function insertId(){
        return $this->insertId;
    }

    public function insert(Array $data = []){
        
        $data = $this->beforeInsert($data);
        
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

    public function update(Array $data){
        
        $data = $this->beforeUpdate($data);
        
        $query = 'UPDATE ' . $this->from . ' SET ';
        $values = [];

        foreach ($data as $column => $val){
            $values[] = $column . '=' . $this->escape($val);
        }

        $query .= (is_array($data) ? implode(',', $values) : $data);

        if (!is_null($this->where)) { $query .= ' WHERE ' . $this->where;}

        if (!is_null($this->orderBy)) {$query .= ' ORDER BY ' . $this->orderBy;}

        if (!is_null($this->limit)) {$query .= ' LIMIT ' . $this->limit;}
        
        $query = $this->query($query);

        if ($query){
            $this->insertId = $this->pdo->lastInsertId();
            if(isset($this->where['id'])){
                return $this->afterUpdate($this->where['id']);
            }            
        }
        
        return $this->query($query);
    }

    public function delete(){
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
