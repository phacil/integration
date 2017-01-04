<?php

use Phacil\Integration\Integration;
/**
 * 
 * @return \Phacil\Integration\Database\Query
 */

function query()
{
    $pdo = Integration::exec(Integration::getActualConfig());
    return new \Phacil\Integration\Database\Query($pdo);
}