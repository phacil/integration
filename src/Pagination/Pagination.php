<?php

namespace Phacil\Component\Integration\Pagination;

use Phacil\Component\Integration\Database\Query;
use Phacil\Component\HTML\HTML as Html;

use ICanBoogie\Inflector;

class Pagination {
    
    private $page;
    private $orderBy = '';
    private $direction = 'ASC';
    private $query = null;
    private $query_clone = null;
    private $limit = 10;    
    
    private $records = 0;
    private static $num_records = 0;
    private static $total_records = 0;
    
    private static $container = array('tag'=>'ul', 'class'=>'');
    private static $list = array('tag'=>'li', 'class'=>'', 'classActive'=>'active', 'classDisabled'=>'disabled');
    
    private $args = [];
    private static $request_args = [];
    
    function __construct(Query $query, $args = []) {
               
        $this->setArgs($args);
        
        $this->query = $query;        
        $this->query_clone = clone $this->query;
        
        return $this;
    }
    
    function getPage() {
        return $this->page;
    }

    function getOrderBy() {
        return $this->orderBy;
    }

    function getDirection() {
        return $this->direction;
    }

    function getLimit() {
        return $this->limit;
    }

    function getArgs() {
        return $this->args;
    }

    function setPage($page) {
        $this->args['page'] = $this->page = $page;
        return $this;
    }

    function setOrderBy($orderBy) {
        $this->args['order'] = $this->orderBy = $orderBy;
        return $this;
    }

    function setDirection($direction) {
        $this->args['direction'] = $this->direction = $direction;
        return $this;
    }

    function setLimit($limit) {
        $this->args['limit'] = $this->limit = $limit;
        return $this;
    }

    function setArgs($args) {
        
        $this->setPage(isset($args['page'])?$args['page']:1);
        $this->setLimit(isset($args['limit'])?$args['limit']:$this->limit);
        $this->setDirection(isset($args['direction'])?$args['direction']:$this->direction);
        $this->setOrderBy(isset($args['order'])?$args['order']:null);
        
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

    public function get() {
        
        $records = $this->query
                        ->orderBy($this->orderBy, $this->direction)
                        ->limit($this->limit)
                        ->offset(($this->page - 1) * $this->limit)
                        ->get();
        //pr($this);
        self::$num_records = $this->records = $this->query->getNumRows();
        self::$total_records = $this->total_records();
        
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
    
//    public static function pages() {        
//        $paging_options = array(
//            'page'=> isset($this->['page'])?$request['args']['page']:1,
//            'limit'=>isset($request['args']['limit'])?$request['args']['limit']:self::$limit,
//            'records'=>self::$records,
//            'total_records'=>self::$total_records,
//        );
//        
//        return new Paging(self::$container, self::$list, $paging_options);
//    }
//    
    private static function __setDirection($field){
        $args = self::$request_args;
        
        if(isset($args['order']) && $args['order']==$field && $args['direction']=='ASC'){
            return 'DESC';
        }
        
        return 'ASC';
    }

    public static function order($field, $label = null) {        
        $text = ($label)?$label:self::__getInflactor()->camelize($field);
        $direction = self::__setDirection($field);
        $rota = Route::url()->args(array('order'=>$field, 'direction'=>  $direction));
        return Html::a($text)->href($rota)->output();
    }
    
}
