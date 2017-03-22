<?php

use Phacil\Integration\Integration;
use Phacil\Integration\Database\Query;
use Phacil\Integration\Pagination\Paginate;
use Phacil\Integration\Database\Raw;

/**
 * 
 * @return \Phacil\Integration\Database\Query
 */
function query($table)
{
    $pdo = Integration::exec(Integration::getActualConfig());
    return (new Query($pdo))->from($table);
}

/**
 * 
 * @return \Phacil\Integration\Pagination\Paginate
 */
function paginate(){
     if(!is_null(Paginate::getInstance())){
        return Paginate::getInstance();
    }
    return new Paginate();
}

/**
 * 
 * @return \Phacil\Integration\Pagination\Raw
 */
function raw($value, $bindings = []){
    return new Raw($value, $bindings);
}
