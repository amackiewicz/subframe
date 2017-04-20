<?php namespace webcitron\Subframe;

use \webcitron\Subframe\Application;
use webcitron\Subframe\Debug;

class StorageMemcache {
    
    private static $arrServers = [];
    private static $arrRunningInstances = [];
    private static $boolConfigLoaded = false;
    
    private $arrCurrentConfig = array();
    private $objMemcached = null;

    private $boolEnabled = false;
    
    public static function addServer ($numEnvironment, $strName, $numPort, $strKeyPattern = '') {
        self::$arrServers[$numEnvironment][$strName] = array(
            'numPort' => $numPort, 
            'strKeyPattern' => $strKeyPattern
        );
    }
    
    private static function loadConfig () {
        $objApplication = Application::getInstance();
        require $objApplication->strDirectory.'/config/memcache.php';
        self::$boolConfigLoaded = true;
    }
    
    public function __construct ($strServerName) {
        if (class_exists('\Memcache')) {
            $this->boolEnabled = true;
            if (self::$boolConfigLoaded === false) {
                self::loadConfig();
            }
            
            $numCurrentEnvironment = Application::currentEnvironment();
            
            if (!isset(self::$arrRunningInstances[$numCurrentEnvironment][$strServerName])) {
                $arrConfig = self::$arrServers[$numCurrentEnvironment][$strServerName];
                $objMemcache = new \Memcache();
                $objMemcache->connect('localhost', $arrConfig['numPort']);
                Debug::log('Connected to localhost:'.$arrConfig['numPort'], 'memcache');
                self::$arrRunningInstances[$numCurrentEnvironment][$strServerName] = $objMemcache;
                $this->arrCurrentConfig = $arrConfig;
            }
            $this->objMemcache = self::$arrRunningInstances[$numCurrentEnvironment][$strServerName];
        }
    }
    
    public function get ($strKeyName, $mulDefaultValue = null) {
        $mulValue = false;
        if ($this->boolEnabled === true) {
            $strMemcacheKey = sprintf($this->arrCurrentConfig['strKeyPattern'], $strKeyName);
            $mulValue = $this->objMemcache->get($strMemcacheKey);
        }

        if ($mulValue === false) {
            $mulValue = $mulDefaultValue;
        }
        
        return $mulValue;
    }
    
    public function clear () {
        if ($this->boolEnabled === true) {
            $this->objMemcache->flush();
        }
    }

    public function set ($strKeyName, $mulValue, $numLifetime = 0) {
        $boolReturn = false;
        if ($this->boolEnabled === true) {
            $strMemcacheKey = sprintf($this->arrCurrentConfig['strKeyPattern'], $strKeyName);
            $boolReturn = $this->objMemcache->set($strMemcacheKey, $mulValue, 0, $numLifetime);
        }
        
        return $boolReturn;
    }
    
    
}