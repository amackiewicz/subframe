<?php namespace webcitron\Subframe;

use webcitron\Subframe\Request;

class Controller {
    
    public $strControllerName = '';
//    public $strView = '';
//    public $strLayout = '';
//    public $arrViewData = array();
    
    public function withArgs() {
        $arrArgs = Request::args();
        return !empty($arrArgs);
    }
    
//    public function setLayout ($strLayout) {
//        $this->strLayout = $strLayout;
//    }
    
    public function viewData($arrData) {
        $this->arrViewData = $arrData;
    }
}