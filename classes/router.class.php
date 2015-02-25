<?php

class Router {
    
    private static $objInstance = null;
    public $arrRoutes = array();
    
    private function __construct() {}
    
    public static function getInstance() {
        if (self::$objInstance === null) {
            self::$objInstance = new Router();
        }
        return self::$objInstance;
    }
    
    public function loadRoutes () {
        require APP_DIR.'/config/routes.php';
    }
    
    public static function addRoute ($strRouteName, $strUri, $strControllerAction) {
        $objRouter = self::getInstance();
        $arrControllerActionTokens = explode('/', $strControllerAction);
        
        $objRoute = new Route($arrControllerActionTokens[0], $arrControllerActionTokens[1], $strRouteName);
        $objRoute->setUri($strUri);
        $objRoute->recognizeSetParams($strUri);
        $objRouter->arrRoutes[$strRouteName] = $objRoute;
    }
    
    
    public function dispath () {
        $objRequest = Request::getInstance();
        $strCurrentUri = $objRequest->getUri();
        $objCurrentRoute = $this->findRoute($strCurrentUri);
        return $objCurrentRoute;
    }
    
    private function findRoute ($strUri) {
        $objRoute = null;
        $arrHits = array();
        foreach ($this->arrRoutes as $objRoute) {
            $strPattern = sprintf('%s', preg_replace('/\{[^}]+\}/', '([^\/]+)', $objRoute->strUri)); 
//            echo $strUri .' -> '.$strPattern.'<br />';
            
            if (@preg_match_all($strPattern, $strUri, $arrHits) === 1) {
                if (!empty($arrHits)) {
                    Request::setParams($arrHits[0]);
                }
                break;
            }
        }
        
        if (empty($objRoute)) {
            echo "ERROR! ".__FILE__.'::'.__FUNCTION__.'#'.__LINE__;
            exit();
        }
        return $objRoute;
    }
    
    
    public function getRouteByName($strRouteName) {
        return $this->arrRoutes[$strRouteName];
    }
    
    
}