<?php namespace webcitron\Subframe;


class CssController {
    
    public static $objInstance = null;
    public $arrStylesheetsToLoad = array();
    
    public $strForceCssFile = '';
    
    public static function getInstance () {
        if (self::$objInstance === null) {
            self::$objInstance = new CssController();
        }
        return self::$objInstance;
    }
    
    public function forceCssFile ($strFile) {
        $this->strForceCssFile = $strFile;
    }
    
    private function __construct () {}
    
    public function render ($strApplicationName) {
        if (!empty($this->strForceCssFile)) {
            $strCssFile = $this->strForceCssFile;
        } else {
            $objRouter = Router::getInstance();
            $objCurrentRoute = $objRouter->getCurrentRoute();
            $strCssFile = $objCurrentRoute->strRouteName.'_'.$objCurrentRoute->strMethodName;
        }
        $strCssHhtml = sprintf('<link rel="stylesheet" href="/%s/css/%s.css" />', $strApplicationName, $strCssFile);
        
        return $strCssHhtml;
    }
    
    public static function addStylesheets ($arrCssFiles) {
        $objCssController = self::getInstance();
        if (!is_array($arrCssFiles)) {
            $arrCssFiles = array($arrCssFiles);
        }
        $objCssController->arrStylesheetsToLoad = array_unique(array_merge($objCssController->arrStylesheetsToLoad, $arrCssFiles));
    }
    
}