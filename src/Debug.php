<?php namespace webcitron\Subframe;

use webcitron\Subframe\Application;

class Debug
{
    
    private static $boolIsEnabled = null;
    private static $arrMessages = array();
    
    public static function isEnabled () {
        if (self::$boolIsEnabled === null) {
            if (Application::currentEnvironment() !== Application::ENVIRONMENT_PRODUCTION) {
                self::$boolIsEnabled = true;
            } else {
                self::$boolIsEnabled = false;
            }
        }
        return self::$boolIsEnabled;
    }
    
    public static function log ($strContent, $strPrefix = '') {
        if (!empty($strPrefix)) {
            $strContent = sprintf("[%s]\t%s", $strPrefix, $strContent);
        }
        self::$arrMessages[] = $strContent.PHP_EOL;
    }
    
    public static function output () {
        self::log(sprintf('current environment: %s', Application::currentEnvironment()), 'core');
        $strContainer = "<pre style='border:1px solid #888; margin:20px; padding: 20px; background-color:#f8f8f8;'>%s</pre>";
        $strContent = join('', self::$arrMessages);
        
        $strOutput = sprintf($strContainer, $strContent);
        return $strOutput;
    }

}
