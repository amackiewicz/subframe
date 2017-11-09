<?php namespace webcitron\Subframe;


class CssController {
    
    public static $objInstance = null;
    public $arrStylesheetsToLoad = array();
    public $strForceCssFile = '';
    private $strCssHost = 'https://static.imged.pl';
    
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
        $numDeployVersionNumber = file_get_contents(APP_DIR.'/../deploy-version.php');
        if (!empty($numDeployVersionNumber)) {
            $this->numDeployVersion = trim($numDeployVersionNumber);
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
        $strCssFullPath = '';
        if (!empty($this->strForceCssFile)) {
            $strCssFile = $this->strForceCssFile;
        } else {
            $objRouter = Router::getInstance();
            $objCurrentRoute = $objRouter->getCurrentRoute();
            $strCssFile = $objCurrentRoute->strRouteName.'_'.$objCurrentRoute->strMethodName;
        }
        $numEnvironment = Application::currentEnvironment();

        if ($numEnvironment === Application::ENVIRONMENT_DEV) {
            $strApplicationBaseUrl = \webcitron\Subframe\Application::url(); 
            $strCssFullPath = sprintf('%s/%s/css/%s.css', 
                $strApplicationBaseUrl, 
                $strApplicationName, 
                // $this->numDeployVersion, 
                $strCssFile
            );

        } else {
            $strCssPrefix = $this->strCssHost;
            if ($numEnvironment === Application::ENVIRONMENT_RC) {
                $strCssPrefix .= '/rc';
            }
            $strCssFullPath = sprintf('%s/assets/v%d/css/%s.css', 
                $strCssPrefix, 
                $this->numDeployVersion, 
                $strCssFile
            );
        }

        $strCssHhtml = '';
        if (!empty($strCssFullPath)) {
            $strCssHhtml = '<link rel="stylesheet" href="'.$strCssFullPath.'" />';
        }
        


            // if ($numEnvironment === Application::ENVIRONMENT_PRODUCTION) {
            //     $strApplicationBaseUrl = 'https://'.\webcitron\Subframe\Application::url(false);
            // } else {
            //     $strApplicationBaseUrl = \webcitron\Subframe\Application::url();    
            // }
            
            // echo $strApplicationBaseUrl;
            // exit();
            // $strCssHhtml = sprintf('<link rel="stylesheet" href="%s/%s/css/cacheversion-%d/%s.css" />', $strApplicationBaseUrl, $strApplicationName, $this->numDeployVersion, $strCssFile);
            //$strCssHhtml = sprintf('<link rel="stylesheet" href="%s/rc/assets/css/v%d/%s.css" />', $this->strCssHost, $this->numDeployVersion, $strCssFile);
        
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