<?php
namespace Phacil\Integration\Adapter;
use PDO;

class Mysql extends BaseAdapter
{
    /**
     * @var string
     */
    const SANITIZER = '`';
    /**
     * @param $config
     *
     * @return mixed
     */
    protected function doConnect($config)
    {
        $connectionString = "mysql:dbname={$config['database']}";

        if (isset($config['host'])) {
            $connectionString .= ";host={$config['host']}";
        }

        if (isset($config['port'])) {
            $connectionString .= ";port={$config['port']}";
        }

        if (isset($config['unix_socket'])) {
            $connectionString .= ";unix_socket={$config['unix_socket']}";
        }
        
        if(isset($config['options'])){
            $options = array_merge([
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_FETCH_TABLE_NAMES => true
            ], $config['options']);
        }

        $this->pdo = new PDO($connectionString, $config['username'], $config['password'], $options);

        if (isset($config['charset'])) {
            $this->pdo->prepare("SET NAMES '{$config['charset']}'")->execute();
        }

        if(isset($config['collation'])){
            $this->pdo->prepare("SET NAMES '".$config['charset']."' COLLATE '".$config['collation']."'")->execute();
        }

        return $this->pdo;        
    }
}
