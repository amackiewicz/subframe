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
        $arrReturn = array();
        $objApplicationContext = \webcitron\Subframe\Application::getInstance();
        require APP_DIR.'/'.$objApplicationContext->strName.'/config/rpcapi.php';
//        exit();
//        echo '<pre>';
//        var_dump($strMethodPointer);
//        var_dump($this->arrAllowedMethods);
//        echo '</pre>';
//        exit();
        if (in_array($strMethodPointer, $this->arrAllowedMethods)) {
//            echo 'not in array'; exit();
//            $arrReturn['error'] = 'security error';
//        } else {
//            echo 'in array'; exit();
            $arrMethodPointerTokens = explode('.', $strMethodPointer);
            $strMethodName = array_pop($arrMethodPointerTokens);
            $strClassFullPath = sprintf('\\backend\\%s', join('\\', $arrMethodPointerTokens));
//            echo $strClassFullPath.'::'.$strMethodName;
//            exit();
//            $objSpecifiedBoard = new $strBoardFullPath();
            $objBoardMethod = new \ReflectionMethod($strClassFullPath, $strMethodName);
//            $arrRequestParams = Request::getParams();
            $objResponse = $objBoardMethod->invokeArgs(null, $arrParams);
            
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