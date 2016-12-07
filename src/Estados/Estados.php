<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Component\Integration\Estados;
use Phacil\Component\Integration\ORM\Model;

/**
 * Description of Estados
 *
 * @author alisson
 */
class Estados extends Model {

    function __construct()
    {
        parent::__construct();
        $this->belongs_to('paises\paises', ['foreign_key'=>'pais_id']);
    }

}
