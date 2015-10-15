<?php namespace webcitron\Subframe;

use webcitron\Subframe\Templater;

class Templater {
    
    private $objSpecifiedTemplater = null;
    
    public function __construct()
    {
        $strTemplaterName = Config::get('templater');
        $this->objSpecifiedTemplater = new $strTemplaterName;
    }
    
    public static function createSpecifiedTemplater($strTemplaterName) {
//        echo $strTemplaterName;exit();
        $strTemplaterName = strtolower($strTemplaterName);
        switch ($strTemplaterName) {
            case 'blitz':
                $objTemplater = Templater\Blitz::getInstance();
                break;
            case 'twig':
                $objTemplater = Templater\Twig::getInstance();
                break;
        }
//        $strTemplaterClassName = sprintf('Templater%s', ucfirst($strTemplaterName));
//        $fnTemplaterReflection = new \ReflectionMethod($strTemplaterClassName, 'getInstance');
//        $objTemplater = $fnTemplaterReflection->invoke($strTemplaterClassName);
//        echo $strTemplaterName;
//        exit();
//        $strTemplaterClassName = sprintf('Templater%s', ucfirst($strTemplaterName));
//        $objTemplater = Templater\$strClassName::getInstance();
        return $objTemplater;
    }
    
}



