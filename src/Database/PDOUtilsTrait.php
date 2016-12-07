<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Component\Integration\Database;

/**
 * Description of PDOUtilsTrait
 *
 * @author alisson
 */
trait PDOUtilsTrait {
    public function escape($data) {
        return $this->pdo->quote(trim($data));
    }
}
