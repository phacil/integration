<?php
//PIXIE
namespace Phacil\Integration\Adapter;

abstract class BaseAdapter
{
    /**
     * @var string
     */
    protected $pdo = null;
    const SANITIZER = '`';
    /**
     * @param $config
     *
     * @return \PDO
     */
    public function __construct($config)
    {
        if (isset($config['options']) === false) {
            $config['options'] = [];
        }
        try {
            return $this->doConnect($config);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    /**
     * @param $config
     *
     * @return mixed
     */
    abstract protected function doConnect($config);
    
    /**
     * @param $config
     *
     * @return mixed
     */
    public function connection(){
        return $this->pdo;
    }
}
