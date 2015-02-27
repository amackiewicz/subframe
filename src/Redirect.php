<?php namespace webcitron\Subframe;

use webcitron\Subframe\Url;

class Redirect {
    
    public static function route($strRouteName, $arrParams = array(), $numCode = 301) {
        $strUri = Url::route($strRouteName, $arrParams);
        $strHeader = sprintf('Location: %s', $strUri);
        header($strHeader, true, $numCode);
        exit();
    }
    
}