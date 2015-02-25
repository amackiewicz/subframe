<?php
class Config {
    
    public static $arrInstances = array();
    private $arrOptions = array();
    
    private function __construct() {}
    
    public static function getInstance($strConfigName = 'core') {
        if (!isset(self::$arrInstances[$strConfigName])) {
            self::$arrInstances[$strConfigName] = new Config($strConfigName);
        }
        return self::$arrInstances[$strConfigName];
    }
    
    public static function setTemplater($strTemplater) {
        $objConfig = Config::getInstance();
        $objConfig->set('templater', $strTemplater);
    }
    
    public function set($strOption, $strValue) {
        $this->arrOptions[$strOption] = $strValue;
    }
    
    public static function get($strConfigName, $strOption) {
        $objConfig = self::getInstance($strConfigName);
        return $objConfig->arrOptions[$strOption];
        
    }
    
}

