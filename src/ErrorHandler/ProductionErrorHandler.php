<?php namespace webcitron\Subframe\ErrorHandler;

class ProductionErrorHandler
{
    
    public function __construct () {
        ini_set('log_errors',1); 
        ini_set('display_errors',0); 
    }

}