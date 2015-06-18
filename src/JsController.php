<?php namespace webcitron\Subframe;


class JsController {
    
    public static $objInstance = null;
    public $boolRunJs = false;
    public $arrScriptsToLoad = array();
    
    public static function getInstance () {
        if (self::$objInstance === null) {
            self::$objInstance = new JsController();
        }
        return self::$objInstance;
    }
    
    private function __construct () {}
    
    public function render ($strApplicationName) {
        if ($this->boolRunJs !== true) {
            return;
        }
        $strApplicationBaseUrl = \webcitron\Subframe\Application::url();
        $strLaunchCode = sprintf('<script type="text/javascript" src="%s/js/subframe/vendor/head/dist/1.0.0/head.min.js"></script>', $strApplicationBaseUrl).PHP_EOL;
        $strLaunchCode .= sprintf('<script type="text/javascript" src="%s/js/subframe/Launcher.js"></script>', $strApplicationBaseUrl).PHP_EOL;
        $strLaunchCode .= '<script>'.PHP_EOL;
        $strLaunchCode .= sprintf('var objLauncher = new Subframe.Lib.Launcher("%s", "%s", ["%s"]);', $strApplicationName, $strApplicationBaseUrl, join('", "', $this->arrScriptsToLoad)).PHP_EOL;
        $strLaunchCode .= 'objLauncher.init();'.PHP_EOL;
        $strLaunchCode .= '</script>'.PHP_EOL;
        return $strLaunchCode;
    }
    
    public static function runJs () {
        $objJsController = self::getInstance();
        $objJsController->boolRunJs = true;
        $arrBacktrace = debug_backtrace();
        $arrContext = $arrBacktrace[1];
        $strJsFilePath = str_replace('\\', '/', substr($arrContext['class'], strpos($arrContext['class'], 'box\\')+4));
        $objJsController->arrScriptsToLoad[] = $strJsFilePath;
    }
    
}