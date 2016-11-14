<?php namespace webcitron\Subframe;

use webcitron\Subframe\Languages;

class JsController {
    
    public static $objInstance = null;
    public $boolRunJs = false;
    public $arrScriptsToLoad = array();
    private $strCurrentCommit = '';
    
    public static function getInstance () {
        if (self::$objInstance === null) {
            self::$objInstance = new JsController();
        }
        return self::$objInstance;
    }
    
    private function __construct () {
        $this->strCurrentCommit = trim(file_get_contents(APP_DIR.'/../current-commit'));
    }
    
    public function render ($strApplicationName, $numEnableCaching = 1) {
        if ($this->boolRunJs !== true) {
            return;
        }
        $this->arrScriptsToLoad = array_unique($this->arrScriptsToLoad);
        $strApplicationBaseUrl = \webcitron\Subframe\Application::url(false);
        
        $objLanguages = Languages::getInstance();
        $strCurrentLanguage = $objLanguages->getCurrentLanguage();
        
        $strPostfixCache = '';
        if ($numEnableCaching === 0) {
            $strPostfixCache = '&_='.time();
        }
        
        $strLaunchCode = '<script>'.PHP_EOL;
        $strLaunchCode .= 'var boolIsPuppiesBlocked = true;'.PHP_EOL;
        $strLaunchCode .= '</script>'.PHP_EOL;
        $strLaunchCode .= sprintf('<script type="text/javascript" src="//%s/subframe/js/adblock-advertisement.js?%s%s"></script>', $strApplicationBaseUrl, $this->strCurrentCommit, $strPostfixCache).PHP_EOL;
//        $strLaunchCode .= sprintf('<script type="text/javascript" src="%s/subframe/js/vendor/head/dist/1.0.0/head.min.js?%s"></script>', $strApplicationBaseUrl, $this->strCurrentCommit).PHP_EOL;
        $strLaunchCode .= sprintf('<script type="text/javascript" src="//%s/bower_components/jquery/dist/jquery.min.js?%s%s"></script>', $strApplicationBaseUrl, $this->strCurrentCommit, $strPostfixCache).PHP_EOL;
        $strLaunchCode .= sprintf('<script type="text/javascript" src="//%s/subframe/js/AssetLoader.js?%s%s"></script>', $strApplicationBaseUrl, $this->strCurrentCommit, $strPostfixCache).PHP_EOL;
        $strLaunchCode .= sprintf('<script type="text/javascript" src="//%s/subframe/js/Launcher.js?%s%s"></script>', $strApplicationBaseUrl, $this->strCurrentCommit, $strPostfixCache).PHP_EOL;
        $strLaunchCode .= '<script>'.PHP_EOL; 
        $strLaunchCode .= sprintf('var objLauncher = new Subframe.Lib.Launcher("%s", "%s", "%s", "%s", %s, ["%s"]);', $strApplicationName, '//'.$strApplicationBaseUrl, $strCurrentLanguage, $this->strCurrentCommit, $numEnableCaching, join('", "', $this->arrScriptsToLoad)).PHP_EOL;
        $strLaunchCode .= 'objLauncher.init();'.PHP_EOL;
        $strLaunchCode .= '</script>'.PHP_EOL;
        return $strLaunchCode;
    }
    
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