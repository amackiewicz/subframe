<?php namespace webcitron\Subframe;

use \webcitron\Subframe\Router;
use \webcitron\Subframe\Templater;
use \webcitron\Subframe\Request;
use \webcitron\Subframe\Board;
use \webcitron\Subframe\Config;

class Application
{
    public static $objInstance = null;
    private $objRouter = null;
    public $objTemplater = null;
    private static $strEnvironment = '';
    private static $strCurrentAppUrl = '';
    
    public $strName = '';
    public $strDirectory = '';
    public $strApplicationClassesPrefix = '';

    public static function getInstance()
    {
        if (self::$objInstance === null) {
            self::$objInstance = new Application();
        }
        return self::$objInstance;
    }

    private function __construct()
    {
        Request::read();
        $this->recognize();
        $this->loadConfig();
    }
    
    private function recognize() {
        $arrDirectoriesToSkip = array('.', '..', 'backend', 'scripts');
        $objHandle = opendir(APP_DIR);
        if ($objHandle !== false) {
            while (false !== ($strResource = readdir($objHandle))) {
                $strConfigFilePath = sprintf('%s/%s/config/app.php', APP_DIR, $strResource);
                if (in_array($strResource, $arrDirectoriesToSkip) || !is_dir(APP_DIR.'/'.$strResource) || !file_exists($strConfigFilePath)) {
                    continue;
                }
                
                require $strConfigFilePath;
                if(!empty($this->currentAppUrl())) {
                    $this->strName = $strResource;
                    $this->strDirectory = sprintf('%s/%s', APP_DIR, $this->strName);
                    $this->strApplicationClassesPrefix = '\\'.$this->strName;
                    break;
                }
            }
            closedir($objHandle);
        }
    }

    private function loadConfig()
    {
        require $this->strDirectory.'/config/app.php';
    }

    public function launch()
    {
        $this->objRouter = Router::getInstance();
//        $this->objRouter->loadRoutes();
        $this->objTemplater = Templater::createSpecifiedTemplater(Config::get('templater'));
        self::$strCurrentAppUrl = $this->currentAppUrl();
        
        $objCurrentRoute = $this->objRouter->dispath();
        $objResponse = Board::launch($objCurrentRoute->strRouteName);
//        $objBoard->launch();
        return $objResponse;
//        echo '<pre>';
//        print_r($objResponse);
//        exit();
//        $objResponse = $objCurrentRoute->launch();
//        if (!empty($objResponse)) {
//            $objResponse->output($objCurrentRoute);
//        }
//        echo $objResponse;
//        $strOutput = $this->objTemplater->renderController($objController);
//
//        $objResponse = new Response();
//        $objResponse->setContent($strOutput);
//        return $objResponse;
    }
    
    public static function url() {
        return self::$strCurrentAppUrl;
    }
    
    private function currentAppUrl() {
        $strResult = '';
        
        
        $objRequest = Request::getInstance();
        $strRequestDomain = $objRequest->domain();
        
        $arrAppUrls = Config::get('appUrls');
        foreach ($arrAppUrls as $strAppUrl) {
            if ($strAppUrl === $strRequestDomain) {
                $strResult = $strRequestDomain;
                self::$strEnvironment = 'PRODUCTION';
                break;
            }
        }
        
        $arrAppDevUrls = Config::get('appDevUrls');
        foreach ($arrAppDevUrls as $strAppUrl) {
            if ($strAppUrl === $strRequestDomain) {
                $strResult = $strRequestDomain;
                self::$strEnvironment = 'DEVELOPMENT';
                break;
            }
        }
        
        return $strResult;
        
    }
    
    public static function environment() {
        return self::$strEnvironment;
    }

}
