<?php namespace webcitron\Subframe;

use webcitron\Subframe\Response;

class Response {
    
    public $arrData = array();
    public $arrMetaData = array();
    public $numCode = 0;
    
    public function setData($arrData) {
        $this->arrData = $arrData;
    }
    
    public static function view($arrViewData = array(), $strViewName = '') {
        $objResponse = Response\View::getInstance($strViewName);
        $objResponse->setData($arrViewData);
        return $objResponse;
    }
    
    
    public function meta($arrMetaData) {
        $this->arrMetaData = $arrMetaData;
        return $this;
    }
    
    public function setStatus ($numStatusCode) {
        $this->numCode = $numStatusCode;
    }
        
    public static function html($strHtmlContent, $arrViewData = array()) {
        exit();
        $objResponse = new Response\Html();
        $objResponse->setContent($strHtmlContent);
        $objResponse->setData($arrViewData);
        return $objResponse;
    }
    
    public static function image($arrParams) {
        $strContent = $arrParams['strContent'];
        $objResponse = Response\Image::getInstance();
        $objResponse->setData(array('strImageContents' => $strContent));
        return $objResponse;
    }
    
    public static function jsonRpc($arrData) {
        $objResponse = Response\JsonRpc::getInstance();
        $objResponse->setData($arrData);
        return $objResponse;
    }
    
}