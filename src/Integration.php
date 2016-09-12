<?php

namespace Phacil\Component\Integration;

use \FluentPDO as FluentPDO;

class Integration {
        
    protected static $dbConfigs = [];
    public static $configName = 'default';
    
    public static function storeConnection($config = [], $configName = 'default'){
        $config['driver']	= isset($config['driver']) ? $config['driver'] : 'mysql';
        $config['host']		= isset($config['host']) ? $config['host'] : 'localhost';
        $config['charset']	= isset($config['charset']) ? $config['charset'] : 'utf8';
        $config['collation']    = isset($config['collation']) ? $config['collation'] : 'utf8_general_ci';
        $config['prefix']	= isset($config['prefix']) ? $config['prefix'] : '';
        $this->prefix		= $config['prefix'];

        if ($config['driver'] == 'mysql' || $config['driver'] == '' || $config['driver'] == 'pgsql'){
            $dsn = $config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['database'];
        }elseif ($config['driver'] == 'sqlite'){
            $dsn = 'sqlite:' . $config['database'];
        }elseif($config['driver'] == 'oracle'){
            $dsn = 'oci:dbname=' . $config['host'] . '/' . $config['database'];
        }

        try{
            $this->pdo = new PDO($dsn, $config['username'], $config['password']);
            $this->pdo->exec("SET NAMES '".$config['charset']."' COLLATE '".$config['collation']."'");
            $this->pdo->exec("SET CHARACTER SET '".$config['charset']."'");
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            self::$dbConfigs[$configName] = new FluentPDO($this->pdo);
            return true;
        }catch (PDOException $e){
            die('Cannot the connect to Database with PDO.<br /><br />'.$e->getMessage());
        }        
    }

    public static function __callStatic($name, $arguments) {
        
        $connection = self::$dbConfigs[self::$configName];
        
        if(method_exists($connection, $name)){
            return call_user_func_array(array($connection, $name), $arguments);
        }else{
              $connection2 = call_user_func_array(array($connection,'from'), (array) $name);
        
//            $args = !empty($arguments)?$arguments:array('1', '1');
//
//            return call_user_func_array(array($connection2,'where'), $args);
              
              return $connection2;
        }
    }
}
