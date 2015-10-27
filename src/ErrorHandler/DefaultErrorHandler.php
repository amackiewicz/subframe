<?php namespace webcitron\Subframe\ErrorHandler;

class DefaultErrorHandler
{
    public function __construct () {
        set_error_handler(array($this, 'handler'), E_ALL);
    }
    
    public function handler () {
        exit();
    }
    
}