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
    
    public static final function answer ($mulResult = null, $mulError = null, $boolDebug = false) {
        $arrResponse = array();
        if (empty($mulError)) {
            $arrResponse['status'] = 1;
            $arrResponse['result'] = $mulResult;
        } else {
            $arrResponse['status'] = 0;
            $arrResponse['error'] = $mulError;
        }
        if ($boolDebug === true) {
//            echo '<pre>';
//            print_r($arrResponse);
//            echo '</pre>';
            echo json_encode($arrResponse);
            echo 'response length: '.strlen(json_encode($arrResponse));
        }
        return $arrResponse;
    }
}