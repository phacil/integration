<?php
namespace Phacil\Integration\Adapter;
use PDO;

class Pgsql extends BaseAdapter
{
    /**
     * @var string
     */
    const SANITIZER = '"';
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

        if(isset($config['options'])){
            $options = array_merge([
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ], $config['options']);
        }

        $this->pdo = new PDO($connectionString, $config['username'], $config['password'], $options);

        if (isset($config['charset'])) {
            $this->pdo->prepare("SET NAMES '{$config['charset']}'")->execute();
        }

        if (isset($config['schema'])) {
            $this->pdo->prepare("SET search_path TO '{$config['schema']}'")->execute();
        }
    }
}
