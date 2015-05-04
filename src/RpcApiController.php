<?php namespace webcitron\Subframe;

class RpcApiController {
    
    public static $objInstance = null;
    private $arrAllowedMethods = array();
    
    public static function allow($strMethodPointer) {
        $objRpcController = \webcitron\Subframe\RpcApiController::getInstance();
        $objRpcController->addToAllowed($strMethodPointer);
    }
    
    public function addToAllowed ($strMethodPointer) {
        $this->arrAllowedMethods[] = $strMethodPointer;
    }
    
    public function fireMethod ($strMethodPointer, $arrParams) {
        $arrResponse = array();
        $objApplicationContext = \webcitron\Subframe\Application::getInstance();
        require APP_DIR.'/'.$objApplicationContext->strName.'/config/rpcapi.php';
        
        if (!in_array($strMethodPointer, $this->arrAllowedMethods)) {
            $objResponse['error'] = 'that rpc method is not allowed do use remotely';
        } else {
            $arrMethodPointerTokens = explode('.', $strMethodPointer);
            $strMethodName = array_pop($arrMethodPointerTokens);
            $strClassFullPath = sprintf('\\backend\\%s', join('\\', $arrMethodPointerTokens));
//            echo $strClassFullPath.'::'.$strMethodName;
//            echo '<pre>';
//            print_r($arrParams);
            $objBoardMethod = new \ReflectionMethod($strClassFullPath, $strMethodName);
            $objResponse = $objBoardMethod->invokeArgs(null, $arrParams);   
//            echo 'done';
//            echo '<Pre>';
//            print_r($objResponse);
//            exit();
        }
        return $objResponse;
    }
    
    public static function getInstance () {
        if (self::$objInstance === null) {
            self::$objInstance = new \webcitron\Subframe\RpcApiController();
        }
        return self::$objInstance;
    }
    
}