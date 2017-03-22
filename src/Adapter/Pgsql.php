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
    
    protected function getColunms($table, $schema, $pdo){
        
        if(count(explode(' as ', strtolower($table))) == 2){
            list($table, $alias) = explode(' as ', $table);
        }else if(count(explode(' ', strtolower($table))) == 2){
            list($table, $alias) = explode(' ', strtolower($table));
        }else{
            $alias = $table;
        }
        
        $sql = "SELECT *
                FROM information_schema.columns
                WHERE table_schema = '{$schema}'
                  AND table_name   = '{$table}'";
        $stmt = $pdo->query($sql);
        $rows = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[$alias . '.'. $row['column_name']] = "$alias.{$row['column_name']}";
        }
        return $rows;
    }

    protected function makeSelect($select, $from = [], $join = [], $pdo = null)
    {
        if(current($select) == '*'){
            $rows = $this->getColunms($from, 'public', $pdo);
            foreach ($join as $table => $value) {
                $rows = array_merge($rows, $this->getColunms($table, 'public', $pdo));
            }
            $select = $rows;
        }
        $selects = $this->arrayStr($select, 'AS');        
        return $selects;
    }
}
