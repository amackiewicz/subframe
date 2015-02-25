<?php
abstract class Url {
    
    public static function route() {
        // dynamic parameteres :(
        $arrParams = func_get_args();
        $strRouteName = array_shift($arrParams);
        $objRouter = Router::getInstance();
        $objRoute = $objRouter->getRouteByName($strRouteName);
        $strUri = $objRoute->buildUri($arrParams);
        
        return $strUri;
    }
    
}