<?php namespace webcitron\Subframe;


class CssController {
    
    public static $objInstance = null;
    public $arrStylesheetsToLoad = array();
    
    public static function getInstance () {
        if (self::$objInstance === null) {
            self::$objInstance = new CssController();
        }
        return self::$objInstance;
    }
    
    private function __construct () {}
    
    public function render ($strApplicationName) {
        $arrHtmlTags = array();
        if (!empty($this->arrStylesheetsToLoad)) {
            foreach ($this->arrStylesheetsToLoad as $strCssFile) {
                $arrHtmlTags[] = sprintf('<link rel="stylesheet" href="/%s/css/%s.css" />', $strApplicationName, $strCssFile);
            }
        }
        $strCssHhtml = join(PHP_EOL, $arrHtmlTags);
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