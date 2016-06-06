<?php namespace webcitron\Subframe;


class LoggedPdo extends \PDO {
    
    private $arrAlreadyLogged = array();
    
    public function __construct($dsn, $username = null, $password = null, $arrOptions = array()) {
        parent::__construct($dsn, $username, $password, $arrOptions);
    }
    
    public function prepare($query, $options = array()) {
        return new LoggedPDOStatement(parent::prepare($query, $options));
    }
    
    public function exec ($strQ) {
//        echo 'EXECUTING <Pre>';
//        print_r($strQ);
//        echo '</pre>';
        return parent::exec($strQ);
    }
    
    private function microtime_float()
{
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    
}