<?php namespace webcitron\Subframe\Response;

use webcitron\Subframe\Templater;

class View extends \webcitron\Subframe\Response /*implements \webcitron\Subframe\IResponse */{
    
    public static $objInstance = null;
    private  $strView = '';
    
    private function __construct($strViewName)
    {
        if (!empty($strViewName)) {
            $this->strView = $strViewName;
        } else {
            $arrDebugBacktrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4);
            $arrCallingControllerAction = array_pop($arrDebugBacktrace);
            $this->strView = sprintf('%s/%s', strtolower($arrCallingControllerAction['class']), $arrCallingControllerAction['function']);
        }
    }
    
    public static function getInstance($strViewName)
    {
        if (self::$objInstance === null) {
            self::$objInstance = new View($strViewName);
        }
        return self::$objInstance;
    }
    
    public function setView($strViewName = '') {
        $this->strView = $strViewName;
    }
    
    public function output()
    {
        $strLayoutName = 'default';
        $strViewName = $this->strView;
        $arrData = $this->arrData;
        $arrMetaData = $this->arrMetaData;
        
        $objTemplater = \webcitron\Subframe\Application::getInstance()->objTemplater;
        $strOutput = $objTemplater->renderResponseView($strLayoutName, $strViewName, $arrData, $arrMetaData);
        
        echo $strOutput;
    }
    
}