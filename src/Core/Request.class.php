<?php namespace Webcitron\Subframe\Core;

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
        return $objRequest->arrParams;
    }
    
    public static function setParams($arrParams) {
        $objRequest = Request::getInstance();
        $objRequest->arrParams = $arrParams;
    }
    
    public static function read() {
        $objRequest = Request::getInstance();
        $objRequest->arrServer = filter_input_array(INPUT_SERVER);
        $objRequest->arrArgs = filter_input_array(INPUT_POST);
        
        $_POST = $_SERVER = array();
    }
    
    private function __construct() {}
    
    public function getUri() {
        return $this->arrServer['REQUEST_URI'];
    }
    
}