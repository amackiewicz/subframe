<?php namespace webcitron\Subframe;


class CssController {
    
    public static $objInstance = null;
    public $arrStylesheetsToLoad = array();
    public $strForceCssFile = '';
    
    private $strCurrentCommit = '';
    
    public static function getInstance () {
        if (self::$objInstance === null) {
            self::$objInstance = new CssController();
        }
        return self::$objInstance;
    }
    
    public function forceCssFile ($strFile) {
        $this->strForceCssFile = $strFile;
    }
    
    private function __construct () {
        $this->strCurrentCommit = trim(file_get_contents(APP_DIR.'/../current-commit'));
    }
    
    public function renderAsync ($strApplicationName) {
        if (!empty($this->strForceCssFile)) {
            $strCssFile = $this->strForceCssFile;
        } else {
            $objRouter = Router::getInstance();
            $objCurrentRoute = $objRouter->getCurrentRoute();
            $strCssFile = $objCurrentRoute->strRouteName.'_'.$objCurrentRoute->strMethodName;
        }
        $strCssHhtml = sprintf("<script>\nvar arrSubframeCssToLoad = ['/%s/css/%s.css'];\n</script>", $strApplicationName, $strCssFile);
        
        return $strCssHhtml;
    }
    
    public function render ($strApplicationName) {
        if (!empty($this->strForceCssFile)) {
            $strCssFile = $this->strForceCssFile;
        } else {
            $objRouter = Router::getInstance();
            $objCurrentRoute = $objRouter->getCurrentRoute();
            $strCssFile = $objCurrentRoute->strRouteName.'_'.$objCurrentRoute->strMethodName;
        }
        $strCssHhtml = sprintf('<link rel="stylesheet" href="/%s/css/%s.css?%s" />', $strApplicationName, $strCssFile, $this->strCurrentCommit);
        
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