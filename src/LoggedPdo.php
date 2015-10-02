<?php namespace webcitron\Subframe;


class LoggedPdo extends \PDO {
    
    private $arrAlreadyLogged = array();
    
    public function __construct($dsn, $username = null, $password = null) {
        parent::__construct($dsn, $username, $password);
    }
    
    public function prepare($query, $options = array()) {
        return new LoggedPDOStatement(parent::prepare($query, $options));
    }
    
    private function microtime_float()
{
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    
}