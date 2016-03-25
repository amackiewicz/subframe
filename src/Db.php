<?php namespace webcitron\Subframe;

use webcitron\Subframe\Application;
use webcitron\Subframe\Debug;

class Db {
    
    private static $arrInstances = [];
    public $objPdo = null;
    private static $arrConnections = [];
    
    private function __construct($strConnectionName, $numCurrentEnvironment) {
        $objApplication = Application::getInstance();
        require $objApplication->strDirectory.'/config/database.php';
        
        $arrConnection = self::$arrConnections[$strConnectionName][$numCurrentEnvironment];
        $strDsn = sprintf(
            "pgsql:host=%s;dbname=%s",
            $arrConnection['server'],
            $arrConnection['db']
        );
        $numCurrentEnv = Application::currentEnvironment();
        switch ($numCurrentEnv) {
            case Application::ENVIRONMENT_PRODUCTION:
                $this->objPdo = new \PDO($strDsn, $arrConnection['auth'][0], $arrConnection['auth'][1], array(
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING
                ));
                break;
            default:
                $this->objPdo = new LoggedPdo($strDsn, $arrConnection['auth'][0], $arrConnection['auth'][1], array(
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING
                ));
                break;
        }
        $this->objPdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->objPdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $this->objPdo->exec("SET NAMES 'UTF8'");
    }
    
    public static function addConnection($strConnectionName, $numEnvironment, $strType, $strServer, $strDbName, $arrAuth) {

        self::$arrConnections[$strConnectionName][$numEnvironment] = array(
            'type' => $strType, 
            'server' => $strServer, 
            'db' => $strDbName, 
            'auth' => $arrAuth
        );
    }
    
    public static function getInstance($strConnectionName = 'default') {
        if (!isset(self::$arrInstances[$strConnectionName])) {
            $numCurrentEnvironment = Application::currentEnvironment();
            \webcitron\Subframe\Debug::log('Connect to DB '.$strConnectionName, 'core-db');
            self::$arrInstances[$strConnectionName] = new Db($strConnectionName, $numCurrentEnvironment);
        }
        
        return self::$arrInstances[$strConnectionName]->objPdo;
    }
    
    public function __destruct() {
        if (is_object($this->objPdo)) {
            Debug::log('Disconnect main DB ', 'core-db');
            unset($this->objPdo);
            $this->objPdo = null;
        }
        if (!empty(self::$arrInstances)) {
            foreach (self::$arrInstances as $strConnectionName => $objInstance) {
                Debug::log('Disconnect DB '.$strConnectionName, 'core-db');
                unset($objInstance);
                $objInstance = null;
            }
            Debug::log('Destroing instances array', 'core-db');
            self::$arrInstances = array();
        }
        
    }
    
}