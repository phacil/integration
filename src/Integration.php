<?php

namespace Phacil\Integration;

use \PDO as PDO;

class Integration {
        
    private static $config = 'default';
    protected static $dbConfigs = [];
    protected static $connections = [];
    
    static function getConfig($config = 'default') {
        if(isset(self::$dbConfigs[$config])){
            return self::$dbConfigs[$config];
        }
        throw new \Exception("Configuração não existe");
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
    
    static function storeConfig($config = [], $configName = 'default'){
        $config['driver']	= isset($config['driver']) ? $config['driver'] : 'mysql';
        $config['host']		= isset($config['host']) ? $config['host'] : 'localhost';
        $config['charset']	= isset($config['charset']) ? $config['charset'] : 'utf8';
        $config['collation']    = isset($config['collation']) ? $config['collation'] : 'utf8_general_ci';
        $config['prefix']	= isset($config['prefix']) ? $config['prefix'] : '';
        
        self::$dbConfigs[$configName] = $config;
    }
    
    public static function exec($configName = 'default'){
        
        $config = self::getConfig($configName);
        $adapter = "\\Phacil\Integration\\Adapter\\" . ucfirst($config['driver']);
        
        return (new $adapter($config))->connection();
    }
}
