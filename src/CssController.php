<?php namespace webcitron\Subframe;


class CssController {
    
    public static $objInstance = null;
    public $arrStylesheetsToLoad = array();
    public $strForceCssFile = '';
    
    private $numDeployVersion = 0;
    
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
        include APP_DIR.'/../deploy-version.php';
        if (!empty($numDeployVersionNumber)) {
            $this->numDeployVersion = $numDeployVersionNumber;
        }
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
        $numEnvironment = Application::currentEnvironment();
        if ($numEnvironment === Application::ENVIRONMENT_PRODUCTION) {
            $objLanguages = Languages::getInstance();
            if ($objLanguages->getCurrentLanguage() === 'pl_PL') {
                $strStaticDomain = '//static.imged.pl';
            } else if ($objLanguages->getCurrentLanguage() === 'es_ES') {
                $strStaticDomain = '//static.imged.es';
            } else {
                $strStaticDomain = '//static.imged.com';
            }
            $strCssHhtml = sprintf('<link rel="stylesheet" href="%s/assets/css/v%d/%s.css" />', $strStaticDomain, $this->numDeployVersion, $strCssFile);
        } else {
            $strApplicationBaseUrl = \webcitron\Subframe\Application::url();
            $strCssHhtml = sprintf('<link rel="stylesheet" href="%s/%s/css/cacheversion-%d/%s.css" />', $strApplicationBaseUrl, $strApplicationName, $this->numDeployVersion, $strCssFile);
        }
        
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