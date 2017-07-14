<?php namespace webcitron\Subframe;

use webcitron\Subframe\Router;
use webcitron\Subframe\Application;

class Route {
    
//    public $strControllerName  = '';
//    public $strActionName = '';
    public $strUri = '';
    public $strLanguage = '';
    public $strRouteFullName = '';
    public $strRouteName = '';
    public $strMethodName = '';
    public $arrParams = array();
    
    public function __construct($strRouteName = '') {
//        $this->strControllerName = $strControllerName;
//        $this->strActionName = $strActionName;
        $this->strRouteName = $strRouteName;
//        $this->strRouteFullName = $strRouteName;
    }
    
    
    public static function addLang($strRouteFullName, $arrLangUris) {
        $objRouter = Router::getInstance();
//        $arrControllerActionTokens = explode('/', $strControllerAction);
        
        $arrRouteMethod = explode('::', $strRouteFullName);
        if (count($arrRouteMethod) == 2) {
            $strRouteName = $arrRouteMethod[0];
            $strRouteMethod = $arrRouteMethod[1];
        } else {
            $strRouteName = $arrRouteMethod[0];
            $strRouteMethod = 'index';
        }
        foreach ($arrLangUris as $strLanguage => $arrUris) {
            if (!is_array($arrUris)) {
                $arrUris = array($arrUris);
            }
            $objRoute = new Route($strRouteName);
            $objRoute->addUris($arrUris);
            $objRoute->setLanguage($strLanguage);
            $objRoute->setMethod($strRouteMethod);
            $objRoute->recognizeSetParams($arrUris);
            $objRoute->strRouteFullName = $strRouteFullName;
            
            $objRouter->arrRoutes[$strLanguage][$strRouteFullName] = $objRoute;
        }
    }
    
    public static function add($strRouteFullName, $arrUris) {
        $objRouter = Router::getInstance();
//        $arrControllerActionTokens = explode('/', $strControllerAction);
        
        $arrRouteMethod = explode('::', $strRouteFullName);
        if (count($arrRouteMethod) == 2) {
            $strRouteName = $arrRouteMethod[0];
            $strRouteMethod = $arrRouteMethod[1];
        } else {
            $strRouteName = $arrRouteMethod[0];
            $strRouteMethod = 'index';
        }
        
         if (!is_array($arrUris)) {
            $arrUris = array($arrUris);
        }

        $objRoute = new Route($strRouteName);
        $objRoute->addUris($arrUris);
        $objRoute->setMethod($strRouteMethod);
        $objRoute->recognizeSetParams($arrUris);
        $objRoute->strRouteFullName = $strRouteFullName;
        
        $objRouter->arrRoutes[$strRouteFullName] = $objRoute;
    }
    
    public static function addReversed ($strUri, $mulRoute) {
        return self::add($mulRoute, $strUri);
    }
    
    public function getParams () {
        return $this->arrParams;
    }
    
    
    public function launch() {
        $strController = sprintf('controller\%s', $this->strControllerName);
        $objController = new $strController();
        $objController->strControllerName = $this->strControllerName;
//        $objController->strView = strtolower($this->strControllerName).'/'.$this->strActionName;
//        $objController->strLayout = 'default';
        $objActionMethod = new \ReflectionMethod($strController, $this->strActionName);
        $arrRequestParams = Request::getParams();
        
        $objResponse = $objActionMethod->invokeArgs($objController, $arrRequestParams);
//        echo '<pre>';
//        print_r($objController);
//        print_r($objActionMethod);
//        print_r($objResponse);
//        exit();
        return $objResponse;
    }
    
    public function buildUri($arrParams = array()) {
        $numSetIndex = 0;
        for ($numSetIndex; $numSetIndex<count($this->arrUris); $numSetIndex++) { 
            $numThisSetParamsCount = 0;
            if (!empty($this->arrParams[$numSetIndex])) {
                $numThisSetParamsCount = count($this->arrParams[$numSetIndex]);
            }
            if (count($arrParams) === $numThisSetParamsCount) {
                break;
            }
        }

        if ($numThisSetParamsCount === 0) {
            $strResult = $this->arrUris[$numSetIndex];
        } else {

            $arrPatterns = array_map(function ($strParamName) {
                return sprintf('{%s}', $strParamName);

            }, $this->arrParams[$numSetIndex]);
            $arrReplaces = $arrParams;

            $strCurrentUri = str_replace(
                array('(', ')', '?'), 
                '', 
                $this->arrUris[$numSetIndex]
            );

            $strResult = str_replace($arrPatterns, $arrReplaces, $strCurrentUri);
        }
        
        $strResult = sprintf('//%s%s', Application::url(false), $strResult);
        return $strResult;
    }
    
    public function addUris ($arrUris) {
        $this->arrUris = $arrUris;
    }
    public function setUri($strUri) {
        $this->strUri = $strUri;
    }
    
    public function setLanguage($strLanguage) {
        $this->strLanguage = $strLanguage;
    }
    
    public function setMethod($strMethodName) {
        $this->strMethodName = $strMethodName;
    }
    
    public function recognizeSetParams($arrUris) {
        $numUriIndex = 0;
        foreach ($arrUris as $strUri) {
            $arrParams= array();
            preg_match_all("/{[^}]*}/", $strUri, $arrParams);
            if (!empty($arrParams[0])) {
                foreach ($arrParams[0] as $strParam) {
                    $this->arrParams[$numUriIndex][] = substr($strParam, 1, -1);
                }
            }
            $numUriIndex++;
        }
    }
}