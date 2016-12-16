<?php

namespace Phacil\Component\Integration;

use \PDO as PDO;

class Integration {
        
    private static $config = 'default';
    protected static $dbConfigs = [];
    
    static function getConfig($config = 'default') {
        if(isset(self::$dbConfigs[$config])){
            return self::$dbConfigs[$config];
        }
        throw new \Exception("Config não esiste");
    }

    static function getDbConfigs() {
        return self::$dbConfigs;
    }

    static function useConfig($config) {
        self::$config = $config;
    }
    
    static function getActualConfig(){
        return self::$config;
    }
    
    public static function storeConnection($config = [], $configName = 'default'){
        $config['driver']	= isset($config['driver']) ? $config['driver'] : 'mysql';
        $config['host']		= isset($config['host']) ? $config['host'] : 'localhost';
        $config['charset']	= isset($config['charset']) ? $config['charset'] : 'utf8';
        $config['collation']    = isset($config['collation']) ? $config['collation'] : 'utf8_general_ci';
        $config['prefix']	= isset($config['prefix']) ? $config['prefix'] : '';
        //self::$prefix		= $config['prefix'];

        if ($config['driver'] == 'mysql' || $config['driver'] == '' || $config['driver'] == 'pgsql'){
            $dsn = $config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['database'];
        }elseif ($config['driver'] == 'sqlite'){
            $dsn = 'sqlite:' . $config['database'];
        }elseif($config['driver'] == 'oracle'){
            $dsn = 'oci:dbname=' . $config['host'] . '/' . $config['database'];
        }

        try{
            $pdo = new PDO($dsn, $config['username'], $config['password']);
            $pdo->exec("SET NAMES '".$config['charset']."' COLLATE '".$config['collation']."'");
            $pdo->exec("SET CHARACTER SET '".$config['charset']."'");
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_FETCH_TABLE_NAMES, true);
            
            self::$dbConfigs[$configName] = $pdo;
            return true;
        }catch (PDOException $e){
            die('Cannot the connect to Database with PDO.<br /><br />'.$e->getMessage());
        }        
    }
}
