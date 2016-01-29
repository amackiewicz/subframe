<?php namespace webcitron\Subframe;

use \webcitron\Subframe\Application;

class StorageMemcache {
    
    private static $arrServers = [];
    private static $arrRunningInstances = [];
    private static $boolConfigLoaded = false;
    
    private $arrCurrentConfig = array();
    private $objMemcached = null;
    
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
        if (self::$boolConfigLoaded === false) {
            self::loadConfig();
        }
        
        $numCurrentEnvironment = Application::currentEnvironment();
        
        if (!isset(self::$arrRunningInstances[$numCurrentEnvironment][$strServerName])) {
            $arrConfig = self::$arrServers[$numCurrentEnvironment][$strServerName];
            $objMemcached = new \Memcached();
            $objMemcached->addServer('localhost', $arrConfig['numPort']);
//            print_r($objMemcached);
//            exit('s');
//            self::$arrRunningInstances[$numCurrentEnvironment][$strServerName]->connect('localhost', $arrConfig['numPort']);
            self::$arrRunningInstances[$numCurrentEnvironment][$strServerName] = $objMemcached;
            $this->arrCurrentConfig = $arrConfig;
        }
        $this->objMemcached = self::$arrRunningInstances[$numCurrentEnvironment][$strServerName];
    }
    
//    
//    public function __destruct () {
//        if (!empty(self::$arrRunningInstances)) {
//            foreach (self::$arrRunningInstances as $numEnvironment => $arrServers) {
//                foreach ($arrServers as $strServerName => $objMemcache) {
//                    self::$arrRunningInstances[$numEnvironment][$strServerName]->close();
//                }
//            }
//        }
//    }
    
    public function stats () {
//        $arrStats = $this->objMemcached->get('artifact_14129159_shows');
        $this->objMemcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
        var_dump($this->objMemcached->getAllKeys());
//        var_dump($this->objMemcached->getServerList());
//        var_dump($this->objMemcached->getAllKeys());
//        var_dump($this->objMemcached->getResultCode().":".$this->objMemcached->getResultMessage());
//        var_dump();
//        echo '<Pre>';
//        print_r($arrStats);
        exit();
    }
    
    public function get ($strKeyName, $mulDefaultValue = null) {
        $strMemcacheKey = sprintf($this->arrCurrentConfig['strKeyPattern'], $strKeyName);
        $mulValue = $this->objMemcached->get($strMemcacheKey);
//        echo 'get: '.$strMemcacheKey;
//        var_dump($mulValue);
//        echo '</pre>';
        if ($mulValue === false) {
            $mulValue = $mulDefaultValue;
        }
        
        return $mulValue;
    }
    
    public function clear () {
        $this->objMemcached->flush();
    }
    
    public function set ($strKeyName, $mulValue, $numLifetime = 0) {
        $strMemcacheKey = sprintf($this->arrCurrentConfig['strKeyPattern'], $strKeyName);
        $boolReturn = $this->objMemcached->set($strMemcacheKey, $mulValue, 0, $numLifetime);
//        echo 'set: '.$strMemcacheKey.'<pre>';
//        var_dump($mulValue);
//        echo '</pre>';
        
        return $boolReturn;
    }
    
//    public function increment ($strKey, $numInitialValue, $numLifetime = 0) {
//        $this->objMemcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);;
//        return $this->objMemcached->increment($strKey, 1, $numInitialValue, $numLifetime);
//    };
    
}