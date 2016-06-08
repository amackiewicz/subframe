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
    public $objLanguages = null;
    public $objTemplater = null;
    
    private static $numCurrentEnvironment = 0;
    private static $strEnvironment = '';
    
    private static $strCurrentAppUrl = '';
    
    public $strName = '';
    public $strDirectory = '';
    public $strApplicationClassesPrefix = '';
    
    private $arrWorkingEnvironments = array();
    
    public $objErrorHandler = null;

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
    
    public function addEnvironment ($numEnvironment, $mulUrls, $strLanguage = 'en') {
        if (is_array($mulUrls)) {
            $arrUrls = $mulUrls;
        } else if (is_string($mulUrls)) {
            $arrUrls = array($mulUrls);
        }
        
        $objConfigLangs = Config::getInstance('_languageByUrl');
        $objConfig = Config::getInstance('_appUrlsByEnvironment');
        $strConfigIndex = sprintf('environment::%d', $numEnvironment);
//        $objConfig->delete($strConfigIndex);
        foreach ($arrUrls as $strUrl) {
            $strValue = $strUrl;
            $objConfig->add($strConfigIndex, $strValue);
            $objConfigLangs->add($strUrl, $strLanguage);
        }
        
        $this->arrWorkingEnvironments[] = $numEnvironment;
    }

    private function __construct()
    {
        Request::read();
        $this->objLanguages = Languages::getInstance();
        $this->recognize();
        $this->setErrorHandler();
        $this->loadConfig();
    }
    
    private function setErrorHandler () {
        $numCurrentEnv = $this->currentEnvironment();
        switch ($numCurrentEnv) {
            case self::ENVIRONMENT_DEV:
            case self::ENVIRONMENT_NIGHTLY:
            case self::ENVIRONMENT_RC:
                $this->objErrorHandler = new ErrorHandler\DevErrorHandler();
                break;
            default:
                $this->objErrorHandler = new ErrorHandler\ProductionErrorHandler();
                break;
            
        }
    }
    
    private function recognize() {
        $arrDirectoriesToSkip = array('.', '..', 'backend');
        $objHandle = opendir(APP_DIR);
        $boolRecognized = false;
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
                    $boolRecognized = true;
                    $this->strName = $strResource;
                    $this->strDirectory = sprintf('%s/%s', APP_DIR, $this->strName);
                    $this->strApplicationClassesPrefix = '\\'.$this->strName;
                    
                    $arrConfigLanguage = Config::get($strCurrentAppUrl, '_languageByUrl');
                    $this->objLanguages->setCurrentLanguage($arrConfigLanguage[0]);
                    break;
                }
            }
            closedir($objHandle);
        }
        if ($boolRecognized === false) {
            exit('Host cant be recognized. Any application URL not match');
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
