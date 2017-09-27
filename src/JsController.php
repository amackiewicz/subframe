<?php namespace webcitron\Subframe;

use webcitron\Subframe\Languages;

class JsController {
    
    public static $objInstance = null;
    public $boolRunJs = false;
    public $arrScriptsToLoad = array();
    // public $arrCustomToLoad = array();
    private $numDeployVersion = 0;
    
    public static function getInstance () {
        if (self::$objInstance === null) {
            self::$objInstance = new JsController();
        }
        return self::$objInstance;
    }
    
    private function __construct () {
        include APP_DIR.'/../deploy-version.php';
        if (!empty($numDeployVersionNumber)) {
            $this->numDeployVersion = $numDeployVersionNumber;
        }
    }
    
    public function render ($strApplicationName, $numEnableCaching = 1) {
        if ($this->boolRunJs !== true) {
            return;
        }
        $this->arrScriptsToLoad = array_unique($this->arrScriptsToLoad);

        $numEnvironment = Application::currentEnvironment();
        $objApp = Application::getInstance();
        
        if ($numEnvironment === Application::ENVIRONMENT_PRODUCTION && $objApp->strName !== 'admin') {
            $strApplicationBaseUrl = 'https://'.\webcitron\Subframe\Application::url(false);
        } else {
            $strApplicationBaseUrl = \webcitron\Subframe\Application::url();
        }

        
        // echo $strApplicationBaseUrl;
        // exit();
        $objApp = \webcitron\Subframe\Application::getInstance();

        $objLanguages = Languages::getInstance();
        $strCurrentLanguage = $objLanguages->getCurrentLanguage();
        
        $strPostfixCache = ''; 
        if ($numEnableCaching === 0) {
            $strPostfixCache = '&_='.time();
        }
         
        $strLaunchCode = '';
        $strLaunchCode .= '<script>'.PHP_EOL;
        $strLaunchCode .= 'var boolIsPuppiesBlocked = true;'.PHP_EOL; 
        $strLaunchCode .= 'var d = new Date();var numJsPointTimestamp = d.getTime();'.PHP_EOL;
        $strLaunchCode .= '</script>'.PHP_EOL;
        $strLaunchCode .= sprintf('<script type="text/javascript" src="%s/subframe/js/cacheversion-%d/adblock-advertisement.js"></script>', $strApplicationBaseUrl, $this->numDeployVersion).PHP_EOL;
        $objRoute = Router::getCurrentRoute();
        $strBoardJsFilename = str_replace('::', '_', $objRoute->strRouteFullName);
        $strLaunchCode .= sprintf('<script type="text/javascript" src="%s/%s/js/cacheversion-%d/board_min/%s.min.js"></script>', $strApplicationBaseUrl, $objApp->strName, $this->numDeployVersion, $strBoardJsFilename).PHP_EOL;
        
        
        
        
        // $strLaunchCode .= sprintf('<script type="text/javascript" src="//%s/%s/js/cacheversion-%d/app.min.js"></script>', $strApplicationBaseUrl, $objApp->strName, $this->numDeployVersion).PHP_EOL;
        $strLaunchCode .= '<script>'.PHP_EOL; 
        
        // $strCustoms = '';
        // if (!empty($this->arrCustomToLoad)) {
        //     $strCustoms = '"'.join('", "', $this->arrCustomToLoad).'"';
        // }
        $strLaunchCode .= sprintf('var objLauncher = new Subframe.Lib.Launcher("%s", "%s", "%s", "%s", %s, ["%s"]);', $strApplicationName, $strApplicationBaseUrl, $strCurrentLanguage, $this->numDeployVersion, $numEnableCaching, join('", "', $this->arrScriptsToLoad)).PHP_EOL;
        $strLaunchCode .= 'objLauncher.init();'.PHP_EOL;
        $strLaunchCode .= '</script>'.PHP_EOL;
        return $strLaunchCode;
    }
    
    // public static function addCustomJs ($strFile) {
    //     $objJsController = self::getInstance();
    //     $objJsController->boolRunJs = true;
    //     $objJsController->arrCustomToLoad[] = $strFile;
    // }
    
    public static function runJs () {
        $objJsController = self::getInstance();
        $objJsController->boolRunJs = true;
        $arrBacktrace = debug_backtrace();
        $arrContext = $arrBacktrace[1];
        if (!empty($arrContext['class'])) {
            $strJsFilePath = str_replace('\\', '/', substr($arrContext['class'], strpos($arrContext['class'], 'box\\')+4));
            $objJsController->arrScriptsToLoad[] = $strJsFilePath;
        }
    }
    
}