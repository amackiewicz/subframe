<?php namespace webcitron\Subframe;

class Request {
    
    public static $objInstance = null;
    private $arrServer = array();
    private $arrArgs = array();
    public $arrParams = array();
    
    public static function getInstance() {
        if (self::$objInstance === null) {
            self::$objInstance = new Request();
        }
        return self::$objInstance;
    }
    
    public static function arg($strArgName) {
        $objRequest = Request::getInstance();
        return $objRequest->arrArgs[$strArgName];
    }
    
    public static function args() {
        $objRequest = Request::getInstance();
        return $objRequest->arrArgs;
    }
    
    public static function getParams () {
        $objRequest = Request::getInstance();
//        $arrReturn = $objRequest->arrParams;
        $arrReturn = array_map(function ($mulParamValue) {
            if (is_numeric($mulParamValue)) {
                $mulOutput = intval($mulParamValue);
            } else {
                $mulOutput = $mulParamValue;
            }
            return $mulOutput;
        }, $objRequest->arrParams);
//        var_dump($arrReturn);
        return $arrReturn;
    }
    
    public static function setParams($arrParams) {
        $objRequest = Request::getInstance();
        $objRequest->arrParams = $arrParams;
    }
    
    public static function read() {
        $objRequest = Request::getInstance();
        $objRequest->arrServer = filter_input_array(INPUT_SERVER);
        $objRequest->arrArgs = filter_input_array(INPUT_POST);
        
        if (!empty($objRequest->arrArgs)) {
            $objRequest->arrArgs = array_map(function($mulElement) {
                $mulOutput = $mulElement;
                if (is_string($mulOutput)) {
                    $mulOutput = trim($mulOutput);
                }
                return $mulOutput;
            }, $objRequest->arrArgs);
        }
        
        $_POST = $_SERVER = array();
    }
    
    private function __construct() {}
    
    public function domain() {
        $strResult = sprintf('%s://%s', $this->arrServer['REQUEST_SCHEME'], $this->arrServer['SERVER_NAME']);
        return $strResult;
    }
    
    public function getUri() {
        return $this->arrServer['REQUEST_URI'];
    }
    
}