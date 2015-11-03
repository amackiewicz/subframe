<?php namespace webcitron\Subframe;

class LoggedPDOStatement {
    
    private $statement;
    public static $arrAlreadyLogged = array();
    
    public function __construct(\PDOStatement $statement) {
        $this->statement = $statement;
    }
    
    public function __call($function_name, $parameters) {
//        echo '_CALL'.PHP_EOL;
//        echo $function_name.PHP_EOL;
//        echo '<pre>';
//        print_r($parameters);
//        echo '</pre>';
        return call_user_func_array(array($this->statement, $function_name), $parameters);
    }
    
    private function microtime_float()
{
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    public function execute ($arrParams = null) {
//        echo 'EXECUTING <Pre>';
//        print_r($this);
//        print_r($arrParams);
//        echo '</pre>';
//        echo '<pre>';
//        print_r( debug_backtrace()) ;
//        echo '</pre>';
        $start = $this->microtime_float();
        $objRes = $this->statement->execute($arrParams);
        $time = $this->microtime_float() - $start;
        if (!in_array($this->statement->queryString, self::$arrAlreadyLogged)) {
            $strClass = 'unknown';
            $strMethod = 'unknown';
            $arrDebug = debug_backtrace();
            if (!empty($arrDebug[1])) {
                $strClass = $arrDebug[1]['class'];
                $strMethod = $arrDebug[1]['function'];
            }
            unset($arrDebug);
            $strDebugString = sprintf("%s \n\tfrom %s::%s() \n\ttime: <span style='color:red;'>%s s.</span>\n", $this->statement->queryString, $strClass, $strMethod, $time);
            Debug::log($strDebugString, 'db-stmt-execute');
            self::$arrAlreadyLogged[] = $this->statement->queryString;
        }
        
        return $objRes;
    }
    
    

}