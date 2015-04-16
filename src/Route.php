<?php namespace webcitron\Subframe;

use webcitron\Subframe\Router;
use webcitron\Subframe\Application;

class Route {
    
//    public $strControllerName  = '';
//    public $strActionName = '';
    public $strUri = '';
    public $strRouteName = '';
    public $arrParams = array();
    
    public function __construct($strRouteName = '') {
//        $this->strControllerName = $strControllerName;
//        $this->strActionName = $strActionName;
        $this->strRouteName = $strRouteName;
    }
    
    
    public static function add($strRouteName, $strUri) {
        $objRouter = Router::getInstance();
//        $arrControllerActionTokens = explode('/', $strControllerAction);
        
        $objRoute = new Route($strRouteName);
        $objRoute->setUri($strUri);
        $objRoute->recognizeSetParams($strUri);
        $objRouter->arrRoutes[$strRouteName] = $objRoute;
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
                $numStartRemovingFrom = count($arrPatterns) - count($arrReplaces);
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