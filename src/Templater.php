<?php namespace webcitron\Subframe;

class Templater {
    
    private $objSpecifiedTemplater = null;
    
    public function __construct()
    {
        $strTemplaterName = Config::get('templater');
        $this->objSpecifiedTemplater = new $strTemplaterName;
    }
    
    public static function createSpecifiedTemplater($strTemplaterName) {
//        $strClassName = 'Templater'.ucfirst($strTemplaterName);
        $objTemplater = TemplaterBlitz::getInstance();
        return $objTemplater;
    }
    
}



