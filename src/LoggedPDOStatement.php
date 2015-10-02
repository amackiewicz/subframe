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
        $start = $this->microtime_float();
        $objRes = $this->statement->execute($arrParams);
        $time = $this->microtime_float() - $start;
        if (!in_array($this->statement->queryString, self::$arrAlreadyLogged)) {
            Debug::log($this->statement->queryString .' (time: <span style="color:red;">'.$time.' s.</span>)', 'db-stmt-execute');
            self::$arrAlreadyLogged[] = $this->statement->queryString;
        }
        
        return $objRes;
    }
    
    

}