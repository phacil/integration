<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Integration\Database;

/**
 * Description of PersistentTrait
 *
 * @author alisson
 */
trait PersistentTrait {
    
    //use PDOUtilsTrait;
    
    public function insertId(){
        return $this->insertId;
    }

    public function insert(Array $data){
        $columns = array_keys($data);
        $column = implode(',', $columns);
        $val = implode(', ', array_map([$this, 'escape'], $data));

        $query = 'INSERT INTO ' . $this->from . ' (' . $column . ') VALUES (' . $val . ')';
        $query = $this->query($query);

        if ($query){
            $this->insertId = $this->pdo->lastInsertId();
            return $this->insertId();
        }
        
        return false;                
    }

    public function update(Array $data){
        $query = 'UPDATE ' . $this->from . ' SET ';
        $values = [];

        foreach ($data as $column => $val){
            $values[] = $column . '=' . $this->escape($val);
        }

        $query .= (is_array($data) ? implode(',', $values) : $data);

        if (!is_null($this->where)) { $query .= ' WHERE ' . $this->where;}

        if (!is_null($this->orderBy)) {$query .= ' ORDER BY ' . $this->orderBy;}

        if (!is_null($this->limit)) {$query .= ' LIMIT ' . $this->limit;}

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
