<?php
/*
*
* @ Package: PDOx - Useful Query Builder & PDO Class
* @ Class: Pdox
* @ Author: izni burak demirtas / @izniburak <info@burakdemirtas.org>
* @ Web: http://burakdemirtas.org
* @ URL: https://github.com/izniburak/PDOx
* @ Licence: The MIT License (MIT) - Copyright (c) - http://opensource.org/licenses/MIT
*
*/

namespace Phacil\Integration\Database;

use Phacil\Integration\Integration;
use \IteratorAggregate;
use \ArrayIterator;
use \PDO as PDO;

class Query implements IteratorAggregate
{
    use SelectTrait,
        JoinTrait, 
        WhereTrait, 
        GroupTrait, 
        OrderTrait, 
        LimitTrait,
        PersistentTrait,
        UtilsTrait,
        HooksTrait;
    
    public $pdo 	= null;

    protected $select 	= '*';
    protected $from 	= null;
    protected $where 	= null;
    protected $limit 	= null;
    protected $offset 	= null;
    protected $join 	= null;
    protected $orderBy 	= null;
    protected $groupBy 	= null;
    protected $having 	= null;
    protected $grouped 	= false;
    protected $numRows 	= 0;
    protected $insertId = null;
    
    protected $query 	= null;
    protected $error 	= null;
    protected $result 	= array();
    protected $prefix 	= null;
    protected $op 	= array('=','!=','<','>','<=','>=','<>');
    
    protected $cache 	= null;
    protected $cacheDir	= null;
    
    protected $queryCount	= 0;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        return $this->pdo;
    }
    
    public function table($from) {
        return $this->from($from);
    }
    
    public function query($query, $all = true, $array = false, $reset = true) {
        if($reset === true){
            $this->reset();	
        }        

        if(is_array($all)){
            $x = explode('?', $query);
            $q = '';

            foreach($x as $k => $v){
                if(!empty($v)){
                    $q .= $v . (isset($all[$k]) ? $this->escape($all[$k]) : '');
                }
            }

            $query = $q;
        }

        $this->query = preg_replace('/\s\s+|\t\t+/', ' ', trim($query));
        $str = stristr($this->query, 'SELECT');

        $cache = false;

        if (!is_null($this->cache)){
            $cache = $this->cache->getCache($this->query, $array);
        }

        if (!$cache && $str){
            $sql = $this->pdo->query($this->query);

            if ($sql) {
                $this->numRows = $sql->rowCount();

                if (($this->numRows > 0)) {
                    if ($all){
                        $q = [];

                        while ($result = ($array == false) ? $sql->fetchAll(PDO::FETCH_OBJ) : $sql->fetchAll(PDO::FETCH_ASSOC)){
                            $q[] = $result;
                        }

                        $this->result = $q[0];
                    }else{
                        $q = ($array == false) ? $sql->fetch(PDO::FETCH_OBJ) : $sql->fetch(PDO::FETCH_ASSOC);
                        $this->result = $q;
                    }
                }

                if (!is_null($this->cache)){
                    $this->cache->setCache($this->query, $this->result);
                }

                $this->cache = null;
            }else{
                $this->cache = null;
                $this->error = $this->pdo->errorInfo();
                $this->error = $this->error[2];

                return $this->error();
            }
        }

        elseif ((!$cache && !$str) || ($cache && !$str)){
            $this->cache = null;
            $this->result = $this->pdo->query($this->query);

            if (!$this->result) {
                $this->error = $this->pdo->errorInfo();
                $this->error = $this->error[2];

                return $this->error();
            }
        }else{
            $this->cache = null;
            $this->result = $cache;
        }

        $this->queryCount++;

        return $this->result;
    }

//    public function cache($time) {
//        $this->cache = new QueryBuilderCache($this->cacheDir, $time);
//        return $this;
//    }

    public function queryCount(){
        return $this->queryCount;
    }

    public function getQuery(){
        return $this->query;
    }
    
    protected function buildQuery(){
        
        $query = 'SELECT ' . $this->select . ' FROM ' . $this->from;
        
        $query .= $this->isNotNullReturn($this->join);
        $query .= $this->isNotNullReturn($this->where, ' WHERE ');
        $query .= $this->isNotNullReturn($this->groupBy, ' GROUP BY ');
        $query .= $this->isNotNullReturn($this->having , ' HAVING ');
        $query .= $this->isNotNullReturn($this->orderBy, ' ORDER BY ');
        $query .= $this->isNotNullReturn($this->limit, ' LIMIT ');
        $query .= $this->isNotNullReturn($this->offset, ' OFFSET ');
        
        return $query;
    }

    protected function reset(){
        $this->select	= '*';
        $this->from		= null;
        $this->where	= null;
        $this->limit	= null;
        $this->orderBy	= null;
        $this->groupBy	= null;
        $this->having	= null;
        $this->join		= null;
        $this->grouped	= false;
        $this->numRows	= 0;
        $this->insertId	= null;
        $this->query	= null;
        $this->error	= null;
        $this->result	= [];

        return;
    }
      
    public function error(){
        $msg = '<h1>Database Error</h1>';
        $msg .= '<h4>Query: <em style="font-weight:normal;">"'.$this->query.'"</em></h4>';
        $msg .= '<h4>Error: <em style="font-weight:normal;">'.$this->error.'</em></h4>';
        die($msg);
    }

    public function get($limit = null, $offset = null, $reset = true){
       
        if($limit){
            $this->limit($limit);
        }
        
        if($offset){
            $this->offset($offset);
        }
        
        $all = ($limit == 1)?false:true;
        
        
        return $this->getAll(false, $all, $reset);
    }

    protected function getAll($array = false, $all = false, $reset = true){
        
        $query = $this->buildQuery();
        
        $result = $this->query($this->beforeSelect($query), $all, $array, $reset);
        
        if(!is_array($result)){
            $collection = $this->injectRow($result);
        }else{
            $collection = [];
            foreach($result as $data){
                $collection[] = $this->injectRow($data);
            }
        }       
        
        return $this->afterSelect($collection);
    }
    
    public function getIterator() {
        return new ArrayIterator($this->getAll(false, true, true));
    }

    public function count(){
        $result = $this->getAll(false, false);
        return $this->numRows;
    }
    
    public function _list($key, $value = null){
        $this->select = null;
        $this->select(($value)?array($key, $value):$key);
        $result = $this->getAll(false, true, true);
        $list = array();
        if(is_array($value)){
            
        }else{
            
            $value = (!empty ($value))?$value:$key;
            
            foreach ($result as $k => $row) { 
                foreach ($row as $model => $v) { 
                    $list[$v->$key] = $v->$value;
                }
            }
        }
        return $list;
    }

    public function __call($name, $args) {
        
        if($name == 'list'){
            return call_user_func_array(array($this, '_list'), $args);
        }else{
            $this->from($name);
            
            if(empty($args)){
                return $this;
            }else{
                return $this->where($args);
            }
            
        }
    }
        
    public static function __callStatic($name, $arguments) {
        
        $pdo = Integration::exec(Integration::getActualConfig());
        $connection = new self($pdo);

        if(method_exists($connection, $name)){
            return call_user_func_array(array($connection, $name), $arguments);
        }else{
            $connection2 = call_user_func_array(array($connection,'from'), (array) $name);
            if(empty($arguments)){
                return $connection2;
            }else{
                return call_user_func_array(array($connection2,'where'), $arguments);
            }
        }
    }
 
    private function injectRow($data){
        return new Row($data);
    }
    
    public function getNumRows(){
        return $this->numRows;
    }
    
    function __destruct(){
        $this->pdo = null;
    }
    
    function __toString() {
        return $this->buildQuery();
    }
}
