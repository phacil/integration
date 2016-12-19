<?php

namespace Phacil\Integration\Pagination;

use Phacil\HTTP\Request;

class RouteBuilder {
    
    private $url = [];
    private $request = [];
    private $base = null;

    public function __construct($url = '/') {
        $this->request = Request::info();
        //pr($this->request); exit;
        $this->url['url'] = $url;        
        //pr($this->base);exit;
        return $this;
        
    }
       
    private function initRequestInfo($parts = array()) {
        foreach($parts as $part){
            if(empty($this->url[$part])){
                $this->url[$part] = $this->request[$part];
            }
        }
    }
 
    public function prefix($prefix = ''){
       $this->url['prefix'] = $prefix;
       return $this;
    } 
    
    public function module($module = ''){
        $this->initRequestInfo(array('prefix'));
        $this->url['module'] = $module;
        return $this;
    }
    
    public function controller($controller = ''){
        $this->initRequestInfo(array('prefix', 'module'));
        $this->url['controller'] = $controller;
        return $this;
    }
    
    public function action($action = ''){
        $this->initRequestInfo(array('prefix', 'module', 'controller'));
        $this->url['action'] = $action;
        return $this;
    }
    
    public function params($params = array()){
        $this->initRequestInfo(array('prefix', 'module', 'controller', 'action'));
        $this->url['params'] = $params;
        return $this;
    }
    
    public function args($args = array()){
        $this->initRequestInfo(array('prefix', 'module', 'controller', 'action', 'params'));
        $this->url['args'] = array_merge($this->request['args'], $args);
        return $this;
    }
    
    public function setBase($base = null){
        $this->base = $base;
        return $this;
    }
    
    public function output(){
        return $this->__toString();
    }

    public function __toString() {
        $out = [];
        
        foreach($this->url as $k => $part){
            if(!empty($part)){
                if($k == 'args'){
                    $out2 = array();
                    foreach($part as $idx => $value){
                        $out2[] = $idx . '='.$value;
                    }
                    $out[] = join('/', $out2);
                }else if($k == 'params'){
                    $out[] = join('/', $part);
                }else{
                    if($part == '/'){
                        continue;
                    }
                    $part = ltrim($part, '/');
                    $out[] = $part;
                }
            }
        }
        
        //pr($this->base . join('/', $out));exit;
        $this->base = (defined('ROOT_URL'))?ROOT_URL:null;
       
        return $this->base . join('/', $out);
    } 
}
