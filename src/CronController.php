<?php namespace webcitron\Subframe;

use backend\DbFactory;

class CronController {
    
    public static $objInstance = null;
    
    
//    public function setEnvironment ($strEnvName) {
//        DbFactory::setDefaultConnection($strEnvName);
//    }
    public function fireMethod ($strMethodPointer, $arrParams) {
        $arrMethodPointerTokens = explode('.', $strMethodPointer);
        $strMethodName = array_pop($arrMethodPointerTokens);
        $strClassFullPath = sprintf('\\backend\\cron\\%s', join('\\', $arrMethodPointerTokens));

        $obj = new $strClassFullPath();

        $objMethod = new \ReflectionMethod($strClassFullPath, $strMethodName);
        $objResponse = $objMethod->invokeArgs($obj, $arrParams);   
        return $objResponse;
    }
    
    public static function getInstance () {
        if (self::$objInstance === null) {
            self::$objInstance = new \webcitron\Subframe\CronController();
        }
        return self::$objInstance;
    }
    
}