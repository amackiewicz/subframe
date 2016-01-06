<?php namespace webcitron\Subframe;

abstract class Url {
    
    public static function route($strRouteName, $arrParams = array()) {
        if (strpos($strRouteName, '::') === false) {
            $strRouteName .= '::index';
        }
        $objRouter = Router::getInstance();
        $objRoute = $objRouter->getRouteByName($strRouteName);
        $strUri = $objRoute->buildUri($arrParams);
        
        return $strUri;
    }
    
    
}