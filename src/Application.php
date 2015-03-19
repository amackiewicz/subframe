<?php namespace webcitron\Subframe;

use \webcitron\Subframe\Router;
use \webcitron\Subframe\Templater;
use \webcitron\Subframe\Request;
use \webcitron\Subframe\Response;
use \webcitron\Subframe\Config;

class Application
{
    public static $objInstance = null;
    private $objRouter = null;
//    private $objRequest = null;
    public $objTemplater = null;
    private static $strEnvironment = '';
    
    private static $strCurrentAppUrl = '';

    public static function getInstance()
    {
        if (self::$objInstance === null) {
            self::$objInstance = new Application();
        }
        return self::$objInstance;
    }

    private function __construct()
    {
        $this->loadConfig();
        $this->objRouter = Router::getInstance();
        $this->objRouter->loadRoutes();
        $this->objTemplater = Templater::createSpecifiedTemplater(Config::get('templater'));

        Request::read();
    }
    
    

    private function loadConfig()
    {
        require APP_DIR.'/config/app.php';
    }

    public function launch()
    {
        self::$strCurrentAppUrl = $this->currentAppUrl();
        
        $objCurrentRoute = $this->objRouter->dispath();
        $objResponse = $objCurrentRoute->launch();
        if (!empty($objResponse)) {
            $objResponse->output($objCurrentRoute);
        }
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
        foreach ($arrAppUrls as $strAppUrl) {
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
