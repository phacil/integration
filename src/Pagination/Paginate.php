<?php

namespace Phacil\Integration\Pagination;

use Phacil\Integration\Database\Query;
use Phacil\HTML\HTML as Html;
use Phacil\HTTP\Request;
use Phacil\Integration\Pagination\RouteBuilder as Route;

use ICanBoogie\Inflector;

class Paginate {
    
    private $page = null;
    private $limit = null;
    private $orderBy = null;
    private $direction = null;
    
    private $query = null;
    private $query_clone = null;
            
    private $records = 0;
    
    private $total_records = 0;
    
    private $args = [];
    
    public static $container = ['tag'=>'ul', 'class'=>''];
    public static $list = ['tag'=>'li', 'class'=>'', 'classActive'=>'active', 'classDisabled'=>'disabled'];
    private static $num_records = 0;    
    private static $request_args = [];
    
    public function __construct() {        
        
        $this->setArgs(Request::info('args'));
        self::$request_args = Request::info('args');
        
        return $this;
    }
    
    public function getPage() {
        return $this->page;
    }

    public function getOrderBy() {
        return $this->orderBy;
    }

    public function getDirection() {
        return $this->direction;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function page($page) {
        $this->args['page'] = $this->page = $page;
        return $this;
    }

    public function orderBy($orderBy) {
        $this->args['order'] = $this->orderBy = $orderBy;
        return $this;
    }

    public function direction($direction) {
        $this->args['direction'] = $this->direction = $direction;
        return $this;
    }

    public function limit($limit) {
        $this->args['limit'] = $this->limit = $limit;
        return $this;
    }

    public function setArgs($args = []) {
              
        $args = array_merge(self::getRequest_args(), $args);
        
        if(isset($args['page'])){
            $this->page($args['page']);
        }
        
        if(isset($args['limit'])){
            $this->limit($args['limit']);
        }
        
        if(isset($args['order'])){
            if(isset($this->query->model) && !(strpos($args['order'], '.'))){
                $this->orderBy($this->query->model.'.'.$args['order']);
            }else{
                $this->orderBy($args['order']);
            }
        }
        
        if(isset($args['diraction'])){
            $this->direction($args['diraction']);
        }
        
        return $this;
    }
    
    private function __setArgs(){
        
        $args = array_merge(['page'=>1, 'limit'=>10], self::getRequest_args());
        
        if(is_null($this->page) && isset($args['page'])){
            $this->page($args['page']);
        }
        
        if(is_null($this->limit) && isset($args['limit'])){
            $this->limit($args['limit']);
        }
        
        if(is_null($this->direction) && isset($args['direction'])){
            $this->direction($args['direction']);
        }
        
        if(is_null($this->orderBy) && isset($args['order'])){
            $this->orderBy($args['order']);
        }
        
        self::$request_args = [ 'page'=>$this->getPage(),
                                'limit'=>$this->getLimit(),
                                'order'=>$this->getOrderBy(),
                                'direction'=>$this->getDirection()];
        
        return $this;
    }

    private static function __getInflactor(){
        return Inflector::get('pt');
    }
    
    private function total_records(){
                 
        $this->query_clone->limit(null);
        $this->query_clone->offset(null);

        $total_records = $this->query_clone
                                ->select('COUNT(*) as count')
                                ->get(1);

        return $total_records->count;
    }

    public static function numRecords(){
        return self::$num_records;
    }

    public function get(Query $query) {
        
        $this->query = $query;        
        $this->query_clone = clone $this->query;
        
        $records = $this
                        ->__setArgs()
                        ->query
                        ->orderBy($this->orderBy, $this->direction)
                        ->limit($this->limit)
                        ->offset(($this->page - 1) * $this->limit)
                        ->get();

        self::$request_args['records'] = $this->records = $this->query->getNumRows();
        self::$request_args['total_records'] = $this->total_records = $this->total_records();
                
        $this->query->reset();        
        return $records;
    }
    
    public static function getTotalRecords(){
        return self::$total_records;
    }

    /***/
        
    static function setRequestArgs(Array $args){
        self::$request_args = $args;
    }
    
    static function getRequest_args() {
        return self::$request_args;
    }
        
    public static function paging() {
        self::$request_args['container'] = self::$container;
        self::$request_args['list'] = self::$list;
        return new Paging(self::$request_args);
    }
    
    private static function __direction($field){
        $args = self::$request_args;
        
        if(isset($args['order']) && $args['order']==$field && $args['direction']=='ASC'){
            return 'DESC';
        }
        
        return 'ASC';
    }

    public static function sort($field, $label = null) {        
        $text = ($label)?$label:self::__getInflactor()->camelize($field);
        $direction = self::__direction($field);
        $rota = (new Route)->args(array('order'=>$field, 'direction'=>  $direction));
        return Html::a($text)->href($rota)->output();
    }
    
}
