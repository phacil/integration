<?php
namespace Phacil\Integration\Adapter;
use PDO;

class Sqlite extends BaseAdapter
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
    public function doConnect($config)
    {
        $connectionString = 'sqlite:' . $config['database'];

        if(isset($config['options'])){
            $options = array_merge([], $config['options']);
        }

        $this->pdo = new PDO($connectionString, null, null, $options);
        
    }
}
