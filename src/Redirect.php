<?php namespace webcitron\Subframe;

use webcitron\Subframe\Url;

class Redirect {
    
    public static function route($strRouteName, $arrParams = array(), $numCode = 301) {
        $strUri = Url::route($strRouteName, $arrParams);
        $strHeader = sprintf('Location: %s', $strUri);
        header($strHeader, true, $numCode);
        exit();
    }
    
    public static function url ($strUrl, $numCode = 301) {
        $strHeader = sprintf('Location: %s', $strUrl);
        header($strHeader, true, $numCode);
        exit();
    }
    
}