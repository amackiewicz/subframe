<?php

class Templater {
    
    private $objSpecifiedTemplater = null;
    
    public function __construct()
    {
        $strTemplaterName = Config::get('core', 'templater');
        $this->objSpecifiedTemplater = new $strTemplaterName;
    }
    
    public static function createSpecifiedTemplater($strTemplaterName) {
        $strClassName = 'templater'.ucfirst($strTemplaterName);
        $objTemplater = new $strClassName;
        return $objTemplater;
    }
    
}



