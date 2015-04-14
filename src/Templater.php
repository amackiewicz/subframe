<?php namespace webcitron\Subframe;

use webcitron\Subframe\Templater\Blitz;

class Templater {
    
    private $objSpecifiedTemplater = null;
    
    public function __construct()
    {
        $strTemplaterName = Config::get('templater');
        $this->objSpecifiedTemplater = new $strTemplaterName;
    }
    
    public static function createSpecifiedTemplater($strTemplaterName) {
//        $strClassName = 'Templater'.ucfirst($strTemplaterName);
//        $strTemplaterClassName = sprintf('Templater%s', ucfirst($strTemplaterName));
//        $fnTemplaterReflection = new \ReflectionMethod($strTemplaterClassName, 'getInstance');
//        $objTemplater = $fnTemplaterReflection->invoke($strTemplaterClassName);
//        echo $strTemplaterName;
//        exit();
//        $strTemplaterClassName = sprintf('Templater%s', ucfirst($strTemplaterName));
        $objTemplater = Blitz::getInstance();
        return $objTemplater;
    }
    
}



