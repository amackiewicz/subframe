<?php namespace webcitron\Subframe\ErrorHandler;

class DevErrorHandler
{
    
    private $arrErrors = array(
        E_ERROR => 'Error', 
        E_WARNING => 'Warning', 
        E_PARSE => 'Parse', 
        E_NOTICE => 'Notice', 
        E_CORE_ERROR => 'Core Error', 
        E_CORE_WARNING => 'Core Warning', 
        E_COMPILE_ERROR => 'Compile Error', 
        E_COMPILE_WARNING => 'Compile Warning', 
        E_USER_ERROR => 'User Error', 
        E_USER_WARNING => 'User Warning', 
        E_USER_NOTICE => 'User Notice', 
        E_STRICT => 'Strict', 
        E_RECOVERABLE_ERROR => 'Recoverable Error', 
        E_DEPRECATED => 'Deprecated', 
        E_USER_DEPRECATED => 'User Deprecated', 
        E_ALL => 'All'
    );
    
    public function __construct () {
        error_reporting(\E_ALL);
        ini_set('log_errors',0); 
        ini_set('display_errors',1); 
        
        set_error_handler(array($this, 'handler'), E_ALL);
    }
    
    public function handler ($numCode, $strErrorContent, $strFile, $numLine, $arrContext) {
        $strMessage = sprintf('<strong>PHP %s error:</strong> %s in %s on line %d %s', $this->getErrorName($numCode), $strErrorContent, $strFile, $numLine, PHP_EOL);
        exit($strMessage);
    }
    
    private function getErrorName ($numCode) {
        return $this->arrErrors[$numCode];
    }
    
}