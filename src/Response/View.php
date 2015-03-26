<?php namespace webcitron\Subframe\Response;

use webcitron\Subframe\Templater;

class View extends \webcitron\Subframe\Response /*implements \webcitron\Subframe\IResponse */{
    
    public static $objInstance = null;
    private  $strView = '';
    private $strLayoutName = 'default';
    
    private function __construct($strViewName)
    {
        if (!empty($strViewName)) {
            $this->strView = $strViewName;
        } else {
            $arrDebugBacktrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4);
            $arrCallingControllerAction = array_pop($arrDebugBacktrace);
            
            $arrCallerClassTokens = explode('\\', $arrCallingControllerAction['class']);
            $strCallerControllerName = array_pop($arrCallerClassTokens);
            $this->strView = sprintf('%s/%s', strtolower($strCallerControllerName), $arrCallingControllerAction['function']);
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
    
    public function setLayout ($strLayoutName) {
        $this->strLayoutName = $strLayoutName;
        return $this;
    }
    
    public function render($objCurrentRoute) {
        $strLayoutName = $this->strLayoutName;
        $strViewName = $this->strView;
        $arrData = $this->arrData;
        $arrMetaData = $this->arrMetaData;
        $objTemplater = \webcitron\Subframe\Application::getInstance()->objTemplater;
        $strOutput = $objTemplater->renderResponseView($objCurrentRoute, $strLayoutName, $strViewName, $arrData, $arrMetaData);
        
        return $strOutput;
    }
    
    public function output($objCurrentRoute)
    {
        echo $this->render($objCurrentRoute);
    }
    
}