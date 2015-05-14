<?php namespace webcitron\Subframe;

use webcitron\Subframe\Router;
use webcitron\Subframe\Application;

class Route {
    
//    public $strControllerName  = '';
//    public $strActionName = '';
    public $strUri = '';
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
    
    
    public static function add($strRouteFullName, $strUri) {
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
        
        $objRoute = new Route($strRouteName);
        $objRoute->setUri($strUri);
        $objRoute->setMethod($strRouteMethod);
        $objRoute->recognizeSetParams($strUri);
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
        
        
//        echo '<pre>';
//        print_r($arrParams);
////        print_r($arrParams);
//        echo '</pre>';
        $strResult = '';
        if (empty($this->arrParams)) {
            $strResult = $this->strUri;
        } else {
            $arrPatterns = array_map(function ($strParamName) {
                return sprintf('{%s}', $strParamName);
                
            }, $this->arrParams);
            $arrReplaces = $arrParams;

            $strCurrentUri = $this->strUri;
            if (count($arrPatterns) > count($arrReplaces)) {
                
                $numStartRemovingFrom = count($arrPatterns) + (count($arrReplaces) - count($arrPatterns));
                for ($numPatternNo=$numStartRemovingFrom; $numPatternNo<count($arrPatterns); $numPatternNo++) {
//                    echo sprintf('(%s/)?', $arrPatterns[$numPatternNo]) .'->'.$strCurrentUri.'<br />';
                    $strCurrentUri = str_replace(
                        sprintf('(%s/)?', $arrPatterns[$numPatternNo]), 
                        '', 
                        $strCurrentUri
                    );
                }
                
            } else if (count($arrPatterns) === count($arrReplaces)) {
                $strCurrentUri = str_replace(
                    array('(', ')', '?'), 
                    '', 
                    $strCurrentUri
                );

            }
//            echo '<pre>';
//            print_r($arrPatterns);
//            print_r($arrReplaces);
//            echo $strCurrentUri.'x';
//            echo '</pre>';
            $strResult = str_replace($arrPatterns, $arrReplaces, $strCurrentUri);
        }
        
        $strResult = sprintf('%s%s', Application::url(), $strResult);
        return $strResult;
    }
    
    public function setUri($strUri) {
        $this->strUri = $strUri;
    }
    
    public function setMethod($strMethodName) {
        $this->strMethodName = $strMethodName;
    }
    
    public function recognizeSetParams($strUri) {
        $arrParams= array();
        preg_match_all("/{[^}]*}/", $strUri, $arrParams);
        if (!empty($arrParams[0])) {
            foreach ($arrParams[0] as $strParam) {
                $this->arrParams[] = substr($strParam, 1, -1);
            }
        }
    }
}