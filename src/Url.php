<?php namespace webcitron\Subframe;

use webcitron\Subframe\Languages;

abstract class Url {
    
    public static function route($strRouteName, $arrParams = array(), $strLanguage = null) {
        $strUri = '';
        if (empty($strLanguage)) {
            $objLanguages = Languages::getInstance();
            $strLanguage = $objLanguages->getCurrentLanguage();
        }
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