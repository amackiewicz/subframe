<?php namespace webcitron\Subframe;

use \webcitron\Subframe\Router;
use \webcitron\Subframe\Templater;
use \webcitron\Subframe\Request;
use \webcitron\Subframe\Board;
use \webcitron\Subframe\Config;

class Application
{
    
    const ENVIRONMENT_PRODUCTION = 1;
    const ENVIRONMENT_RC = 2;
    const ENVIRONMENT_NIGHTLY = 3;
    const ENVIRONMENT_DEV = 4;
    
    public static $objInstance = null;
    private $objRouter = null;
    public $objTemplater = null;
    
    private static $numCurrentEnvironment = 0;
    private static $strEnvironment = '';
    
    private static $strCurrentAppUrl = '';
    
    public $strName = '';
    public $strDirectory = '';
    public $strApplicationClassesPrefix = '';
    
    private $arrWorkingEnvironments = array();

    public static function getInstance()
    {
        if (self::$objInstance === null) {
            self::$objInstance = new Application();
        }
        return self::$objInstance;
    }
    
    public static function currentEnvironment () {
        return self::$numCurrentEnvironment;
    }
    
    public function addEnvironment ($numEnvironment, $mulUrls) {
        if (is_array($mulUrls)) {
            $arrUrls = $mulUrls;
        } else if (is_string($mulUrls)) {
            $arrUrls = array($mulUrls);
        }
        
        $objConfig = Config::getInstance('_appUrlsByEnvironment');
        $strConfigIndex = sprintf('environment::%d', $numEnvironment);
//        $objConfig->delete($strConfigIndex);
        foreach ($arrUrls as $strUrl) {
            $objConfig->add($strConfigIndex, $strUrl);
        }
        $this->arrWorkingEnvironments[] = $numEnvironment;
    }

    private function __construct()
    {
        Request::read();
        $this->recognize();
        $this->setErrorReporting();
        $this->loadConfig();
    }
    
    private function setErrorReporting () {
        if ($this->currentEnvironment() === self::ENVIRONMENT_DEV) {
            error_reporting(\E_ALL);
        }
    }
    
    private function recognize() {
        $arrDirectoriesToSkip = array('.', '..', 'backend');
        $objHandle = opendir(APP_DIR);
        
        if ($objHandle !== false) {
            while (false !== ($strResource = readdir($objHandle))) {
                $strConfigFilePath = sprintf('%s/%s/config/app.php', APP_DIR, $strResource);
                
                if (!is_dir(APP_DIR.'/'.$strResource) || in_array($strResource, $arrDirectoriesToSkip) || !file_exists($strConfigFilePath)) {
                    continue;
                }
                Config::deleteConfig('_appUrlsByEnvironment');
//                echo $strConfigFilePath.PHP_EOL;
                require $strConfigFilePath;
                $strCurrentAppUrl = $this->currentAppUrl();
                if(!empty($strCurrentAppUrl)) {
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

    public function init () {
        $this->objRouter = Router::getInstance();
        self::$strCurrentAppUrl = $this->currentAppUrl();
    }
    
    public function launch()
    {
        $this->objTemplater = Templater::createSpecifiedTemplater(Config::get('templater'));
        
        $objCurrentRoute = $this->objRouter->dispath();
//        $arrRequestParams = Request::getParams();
        
        if (empty($objCurrentRoute)) {
            $objResponse = Board::launch('Error', 'notFound');
        } else {
            $objResponse = Board::launch($objCurrentRoute->strRouteName, $objCurrentRoute->strMethodName);
        }
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
        $strConfigName = '_appUrlsByEnvironment';
        
        $objRequest = Request::getInstance();
        $strRequestDomain = $objRequest->domain();
        
        foreach ($this->arrWorkingEnvironments as $numEnvironment) {
            $strConfigKeyName = sprintf('environment::%d', $numEnvironment);
            $arrEnvironmentUrls = Config::get($strConfigKeyName, $strConfigName);
            if (empty($arrEnvironmentUrls)) {
                continue;
            }
            foreach ($arrEnvironmentUrls as $strEnvironmentUrl) {
                if ($strEnvironmentUrl === $strRequestDomain) {
                    $strResult = $strRequestDomain;
                    self::$numCurrentEnvironment = $numEnvironment;
                    break;
                }
            }
        }
        
        return $strResult;
    }
    
    public static function environment() {
        return self::$strEnvironment;
    }

}
