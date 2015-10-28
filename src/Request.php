<?php namespace webcitron\Subframe;

class Request {
    
    public static $objInstance = null;
    private $arrServer = array();
    private $arrArgs = array();
    public $arrParams = array();
    private $strRequestDoman = '';
    
    public static function getInstance() {
        if (self::$objInstance === null) {
            self::$objInstance = new Request();
        }
        return self::$objInstance;
    }
    
    public static function arg($strArgName) {
        $objRequest = Request::getInstance();
        return trim($objRequest->arrArgs[$strArgName]);
    }
    
    public static function args() {
        $objRequest = Request::getInstance();
        return $objRequest->arrArgs;
    }
    
    public function setVirtualDomain ($strVirtualDomain) {
        $this->strRequestDoman = $strVirtualDomain;
    }
    
    public static function getParams () {
        $objRequest = Request::getInstance();
//        $arrReturn = $objRequest->arrParams;
//        $arrReturn = array_map(function ($mulParamValue) {
//            if (is_numeric($mulParamValue)) {
//                $mulOutput = intval($mulParamValue);
//            } else {
//                $mulOutput = $mulParamValue;
//            }
//            return $mulOutput;
//        }, $objRequest->arrParams);
////        var_dump($arrReturn);
//        return $arrReturn;
        return $objRequest->arrParams;
    }
    
    public static function setParams($arrParams) {
        $objRequest = Request::getInstance();
        $objRequest->arrParams = $arrParams;
    }
    
    public static function read() {
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        $objRequest = Request::getInstance();
        $objRequest->arrServer = filter_input_array(INPUT_SERVER);
        $objRequest->arrArgs = filter_input_array(INPUT_POST);
        echo '<pre>';
        print_r($objRequest->arrArgs);
        echo '</pre>';
        if (!empty($objRequest->arrArgs)) {
            $objRequest->arrArgs = array_map(function($mulElement) {
                $mulOutput = $mulElement;
                if (is_string($mulOutput)) {
                    $mulOutput = trim($mulOutput);
                }
                return $mulOutput;
            }, $objRequest->arrArgs);
        }
        echo '<pre>';
        print_r($objRequest->arrArgs);
        echo '</pre>';
        $_POST = $_SERVER = array();
    }
    
    private function __construct() {}
    
    public function domain() {
        if (!empty($this->strRequestDoman)) {
            $strResult = $this->strRequestDoman;
        } else {
//            $strResult = sprintf('%s://%s', $this->arrServer['REQUEST_SCHEME'], $this->arrServer['SERVER_NAME']);
            $strResult = sprintf('http://%s',$this->arrServer['SERVER_NAME']);
        }
        return $strResult;
    }
    
    public function getUri() {
        $strUri = '/';
        $numQuestPos = strpos($this->arrServer['REQUEST_URI'], '?');
        if ($numQuestPos !== false) {
            $strUri = substr($this->arrServer['REQUEST_URI'], 0, $numQuestPos);
        } else {
            $strUri = $this->arrServer['REQUEST_URI'];
        }
        return $strUri;
    }
    
    public function protocol() {
        return $this->arrServer['SERVER_PROTOCOL'];
    }
    
}