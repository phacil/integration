<?php
namespace Phacil\Integration\Adapter;
use PDO;

class Pgsql extends BaseAdapter
{
    /**
     * @var string
     */
    const SANITIZER = '"';
    protected $pdo = null;
    /**
     * @param $config
     *
     * @return mixed
     */
    protected function doConnect($config)
    {
        $connectionString = "pgsql:host={$config['host']};dbname={$config['database']}";

        if (isset($config['port'])) {
            $connectionString .= ";port={$config['port']}";
        }

        $this->pdo = new PDO($connectionString, $config['username'], $config['password']);
        
        foreach ($config['options'] as $attr => $value) {
            $this->pdo->setAttribute($attr , $value);
        }

        if (isset($config['charset'])) {
            $this->pdo->prepare("SET NAMES '{$config['charset']}'")->execute();
        }

        if (isset($config['schema'])) {
            $this->pdo->prepare("SET search_path TO '{$config['schema']}'")->execute();
        }
        
        return $this->pdo;
        
    }
    
    protected function getColunms($table, $schema = 'public'){
        
        $sql = "SELECT *
                FROM information_schema.columns
                WHERE table_schema = '{$schema}'
                  AND table_name   = '{$table}'";
        $cols = $this->pdo->prepare($sql)->execute();
        pr($cols); exit;
    }

    protected function makeSelect($select, $from = [], $join = [])
    {
        if(current($select) == '*'){
            $this->getColunms($from);
            pr($join);exit;
        }else if(is_array($select)){
            $selects = $this->arrayStr($select, 'AS');
        }else{
            
        }
        
        return $selects;
    }
}
