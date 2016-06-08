<?php namespace webcitron\Subframe;

abstract class Url {
    
    public static function route($strLanguage, $strRouteName, $arrParams = array()) {
        $strUri = '';
        if (strpos($strRouteName, '::') === false) {
            $strRouteName .= '::index';
        }
        $objRouter = Router::getInstance();
        $objRoute = $objRouter->getRouteByNameAndLang($strRouteName, $strLanguage);
        if (empty($objRoute)) {
            $objRoute = $objRouter->getRouteByName('Homepage');
        }
        
        if (!empty($objRoute)) {
            $strUri = $objRoute->buildUri($arrParams);
        }
        
        return $strUri;
    }
    
    
}