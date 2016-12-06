<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Component\Integration\ORM;

/**
 * Description of ORMQuery
 *
 * @author alisson
 */
class ORMQuery extends \Phacil\Component\Integration\Database\Query{
    
    private function injectRow($data){
        return new ORMRow($data);
    }
    
    protected function getAll($array = false, $all = false, $reset = true){
        $query = 'SELECT ' . $this->select . ' FROM ' . $this->from;

        if (!is_null($this->join)){
            $query .= $this->join;
        }

        if (!is_null($this->where)){
            $query .= ' WHERE ' . $this->where;
        }

        if (!is_null($this->groupBy)){
            $query .= ' GROUP BY ' . $this->groupBy;
        }

        if (!is_null($this->having)){
            $query .= ' HAVING ' . $this->having;
        }

        if (!is_null($this->orderBy)){
            $query .= ' ORDER BY ' . $this->orderBy;
        }

        if (!is_null($this->limit)){
            $query .= ' LIMIT ' . $this->limit;
        }
        
        if (!is_null($this->offset)){
            $query .= ' OFFSET ' . $this->offset;
        }
      
        $result = $this->query($query, $all, $array, $reset);
        
        foreach($result as $data){
           $collection[] = $this->injectRow($data);
        }
        return $collection;
    }
    
}
