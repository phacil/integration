<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Component\Integration\Paises;

use Phacil\Component\Integration\ORM\Model;
/**
 * Description of Paises
 *
 * @author alisson
 */
class Paises extends Model {

    function __construct()
    {
        parent::__construct();
        $this->has_many('estados', ['foreign_key'=>'pais_id']);        
    }
}
