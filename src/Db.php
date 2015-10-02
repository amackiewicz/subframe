<?php namespace webcitron\Subframe;

use webcitron\Subframe\Application;

class Db {
    
    private static $arrInstances = [];
    public $objPdo = null;
    private static $arrConnections = [];
    
    private function __construct($strConnectionName) {
        $objApplication = Application::getInstance();
        require $objApplication->strDirectory.'/config/database.php';
        $arrConnection = self::$arrConnections[$strConnectionName];
        $strDsn = sprintf(
            "pgsql:host=%s;dbname=%s;user=%s;password=%s",
            $arrConnection['server'],
            $arrConnection['db'],
            $arrConnection['auth'][0],
            $arrConnection['auth'][1]
        );
        
//        $this->objPdo = new \PDO($strDsn);
        if (Application::currentEnvironment() !== Application::ENVIRONMENT_PRODUCTION) {
            $this->objPdo = new LoggedPdo($strDsn);
        } else {
            $this->objPdo = new \PDO($strDsn);
        }
        $this->objPdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->objPdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->objPdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }
    
    public static function addConnection($strType, $strServer, $strDbName, $arrAuth, $strConnectionName = '') {
        if (empty($strConnectionName)) {
            $strConnectionName = 'default';
        }
        self::$arrConnections[$strConnectionName] = array(
            'type' => $strType, 
            'server' => $strServer, 
            'db' => $strDbName, 
            'auth' => $arrAuth
        );
    }
    
    public static function getInstance($strConnectionName = 'default') {
        if (!isset(self::$arrInstances[$strConnectionName])) {
            \webcitron\Subframe\Debug::log('Connect to DB '.$strConnectionName, 'core-db');
            self::$arrInstances[$strConnectionName] = new Db($strConnectionName);
        }
        
        return self::$arrInstances[$strConnectionName]->objPdo;
    }
    
//    public function loadRoutes () {
//        require APP_DIR.'/config/routes.php';
//    }
//    
//    public static function addRoute ($strRouteName, $strUri, $strControllerAction) {
//        $objRouter = self::getInstance();
//        $arrControllerActionTokens = explode('/', $strControllerAction);
//        
//        $objRoute = new Route($arrControllerActionTokens[0], $arrControllerActionTokens[1], $strRouteName);
//        $objRoute->setUri($strUri);
//        $objRoute->recognizeSetParams($strUri);
//        $objRouter->arrRoutes[$strRouteName] = $objRoute;
//    }
//    
//    
//    public function dispath () {
//        $objRequest = Request::getInstance();
//        $strCurrentUri = $objRequest->getUri();
//        $objCurrentRoute = $this->findRoute($strCurrentUri);
//        return $objCurrentRoute;
//    }
//    
//    private function findRoute ($strUri) {
//        $objRoute = null;
//        $arrHits = array();
//        foreach ($this->arrRoutes as $objRoute) {
//            $strPattern = sprintf('%s', preg_replace('/\{[^}]+\}/', '([^\/]+)', $objRoute->strUri)); 
////            echo $strUri .' -> '.$strPattern.'<br />';
//            
//            if (@preg_match_all($strPattern, $strUri, $arrHits) === 1) {
//                if (!empty($arrHits)) {
//                    Request::setParams($arrHits[0]);
//                }
//                break;
//            }
//        }
//        
//        if (empty($objRoute)) {
//            echo "ERROR! ".__FILE__.'::'.__FUNCTION__.'#'.__LINE__;
//            exit();
//        }
//        return $objRoute;
//    }
//    
//    
//    public function getRouteByName($strRouteName) {
//        return $this->arrRoutes[$strRouteName];
//    }
    
    
}