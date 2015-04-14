<?php namespace webcitron\Subframe;

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
    
    public static function addAppUrl($strUrl) {
        $objConfig = Config::getInstance();
        $objConfig->add('appUrls', $strUrl);
    }
    
    public static function addAppDevUrl($strUrl) {
        $objConfig = Config::getInstance();
        $objConfig->add('appDevUrls', $strUrl);
    }
    
    public function set($strOption, $strValue) {
        $this->arrOptions[$strOption] = $strValue;
    }
    
    public function add($strOption, $strValue) {
        if (!isset($this->arrOptions[$strOption])) {
            $this->arrOptions[$strOption] = array();
        }
        $this->arrOptions[$strOption][] = $strValue;
    }
    
    public static function get($strOption, $strConfigName = 'core') {
        $arrReturn = null;
        $objConfig = self::getInstance($strConfigName);
        if (isset($objConfig->arrOptions[$strOption])) {
            $arrReturn = $objConfig->arrOptions[$strOption];
        }
        return $arrReturn;
        
    }
    
}

