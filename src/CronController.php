<?php namespace webcitron\Subframe;

class CronController {
    
    public static $objInstance = null;
    
    
    public function fireMethod ($strMethodPointer, $arrParams) {
        $arrResponse = array();
        $objApplicationContext = \webcitron\Subframe\Application::getInstance();
//        echo APP_DIR.'/'.$objApplicationContext->strName.'/config/rpcapi.php';
//        exit();
//        require APP_DIR.'/'.$objApplicationContext->strName.'/config/rpcapi.php';
//        
//        if (!in_array($strMethodPointer, $this->arrAllowedMethods)) {
//            $objResponse['error'] = 'that rpc method is not allowed do use remotely';
//        } else {
            $arrMethodPointerTokens = explode('.', $strMethodPointer);
            $strMethodName = array_pop($arrMethodPointerTokens);
            $strClassFullPath = sprintf('\\backend\\%s', join('\\', $arrMethodPointerTokens));
            
            echo $strClassFullPath.'::'.$strMethodName;
//            
//            echo '<pre>';
//            print_r($arrParams);
//            exit();
            $objMethod = new \ReflectionMethod($strClassFullPath, $strMethodName);
            $objResponse = $objMethod->invokeArgs(null, $arrParams);   
            echo 'done';
            echo '<Pre>';
            print_r($objResponse);
            exit();
//        }
        return $objResponse;
    }
    
    public static function getInstance () {
        if (self::$objInstance === null) {
            self::$objInstance = new \webcitron\Subframe\CronController();
        }
        return self::$objInstance;
    }
    
}