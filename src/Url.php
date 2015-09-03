<?php namespace webcitron\Subframe;

abstract class Url {
    
    public static function route($strRouteName, $arrParams = array()) {
        $objRouter = Router::getInstance();
        $objRoute = $objRouter->getRouteByName($strRouteName);
        $strUri = $objRoute->buildUri($arrParams);
        
        return $strUri;
    }
    
    
}