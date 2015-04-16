<?php namespace webcitron\Subframe;


class Router {
    
    private static $objInstance = null;
    public $arrRoutes = array();
    public $boolRoutesLoaded = false;
    
    private static $objCurrentRoute = null;
    
    private function __construct() {}
    
    public static function getInstance() {
        if (self::$objInstance === null) {
            self::$objInstance = new Router();
            self::$objInstance->loadRoutes();
        }
        return self::$objInstance;
    }
    
    public static function getCurrentRoute () {
        return self::$objCurrentRoute;
    }
    
    public function loadRoutes () {
        require Application::getInstance()->strDirectory.'/config/routes.php';
    }
    
    public function dispath () {
        $objRequest = Request::getInstance();
        $strCurrentUri = $objRequest->getUri();
        self::$objCurrentRoute = $this->findRoute($strCurrentUri);
        return self::$objCurrentRoute;
    }
    
    private function findRoute ($strUri) {
        $objRoute = null;
        $arrHits = array();
        
//        $m = array();
//        @preg_match_all("/p/([^\/]+)/([^\/]+).jpg", '/p/again-the-duty-is-assigned-by-the-queen-and/65.jpg', $m);
//        @preg_match_all("/\/p\/([^\/]+)\/([^\/]+)\.jpg/", '/p/again-the-duty-is-assigned-by-the-queen-and/65.jpg', $m);
//        echo '<pre>';
//        print_r($m);
//        echo '</pre>';
        foreach ($this->arrRoutes as $objRoute) {
            $strPattern = $objRoute->strUri;
            $strPattern = str_replace('/', '\/', $strPattern);
            $strPattern = str_replace('.', '\.', $strPattern);
            $strPattern = str_replace('-', '\-', $strPattern);
//            echo str_replace(array('\/', '\-', '\.'), array('/', '-', '.'), $strPattern) .' -> ';
            $strPattern = sprintf('^%s$', preg_replace('/\{[^}]+\}/', '([^\/]+)', $strPattern)); 
//            echo str_replace(array('\/', '\-', '/.'), array('/', '-', '.'), $strPattern).'<br />';
            $strPattern = '/'.$strPattern.'/';
//            echo $strUri .' -> '.$strPattern.'<br />';
            $numPregMatchResult = @preg_match($strPattern, $strUri, $arrHits);
//            echo '<pre>'.$numPregMatchResult;
//            print_r($arrHits);
//            echo '</pre>';
//            exit();
            if ($numPregMatchResult === 1) {
//                echo 'OK';
//                exit();
                if (!empty($arrHits)) {
                    $arrFilteredHits = array();
//                    echo '<pre>';
//                    print_r($arrHits);
//                    exit();
                    for ($numHit = 1; $numHit<count($arrHits); $numHit+=2) {
                        $arrFilteredHits[] = $arrHits[$numHit];
                    }
//                    echo '<pre>';
//                    print_r($arrFilteredHits);
//                    array_shift($arrHits);
//                    print_r($arrHits);
//                    exit();
                    Request::setParams($arrFilteredHits);
                }
                break;
            }
        }
        
        if (empty($objRoute)) {
            echo "ERROR! ".__FILE__.'::'.__FUNCTION__.'#'.__LINE__;
            exit();
        }
        return $objRoute;
    }
    
    
    public function getRouteByName($strRouteName) {
        return $this->arrRoutes[$strRouteName];
    }
    
    
}