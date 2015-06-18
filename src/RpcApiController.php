<?php namespace webcitron\Subframe;

class RpcApiController {
    
    public static $objInstance = null;
    private $arrAllowedMethods = array();
    public $strErrorHandlerFunction = '';
    
    public static function allow($strMethodPointer) {
        $objRpcController = \webcitron\Subframe\RpcApiController::getInstance();
        $objRpcController->addToAllowed($strMethodPointer);
    }
    
    public static function setErrorHandler ($strFunction) {
        $objRpcController = \webcitron\Subframe\RpcApiController::getInstance();
        $objRpcController->strErrorHandlerFunction = $strFunction;
    }
    
    public function addToAllowed ($strMethodPointer) {
        $this->arrAllowedMethods[] = $strMethodPointer;
    }
    
    public function fireMethod ($strMethodPointer, $arrParams) {
        $objApplicationContext = \webcitron\Subframe\Application::getInstance();
        require APP_DIR.'/'.$objApplicationContext->strName.'/config/rpcapi.php';
        
        if (!in_array($strMethodPointer, $this->arrAllowedMethods)) {
    
            $strInfo = $strMethodPointer .' - that rpc method is not allowed do use remotely';
            $objResponse['error'] = $strInfo;
            if (!empty($this->strErrorHandlerFunction)) {
                $arrErrorHandlerTokens = explode('::', $this->strErrorHandlerFunction);
                $objErrorHandlerReflection = new \ReflectionMethod($arrErrorHandlerTokens[0], $arrErrorHandlerTokens[1]);
                $objErrorHandlerReflection->invoke(null, $strInfo);
            }
        } else {
            $arrMethodPointerTokens = explode('.', $strMethodPointer);
            $strMethodName = array_pop($arrMethodPointerTokens);
            $strClassFullPath = sprintf('\\backend\\%s', join('\\', $arrMethodPointerTokens));
            $objBoardMethod = new \ReflectionMethod($strClassFullPath, $strMethodName);
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