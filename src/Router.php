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
    
    public function findRoute ($strUri, $boolDebug = false) {
        $objRoute = null;
        $arrHits = array();
        
//        $m = array();
//        @preg_match_all("/p/([^\/]+)/([^\/]+).jpg", '/p/again-the-duty-is-assigned-by-the-queen-and/65.jpg', $m);
//        @preg_match_all("/\/p\/([^\/]+)\/([^\/]+)\.jpg/", '/p/again-the-duty-is-assigned-by-the-queen-and/65.jpg', $m);
//        echo '<pre>';
//        print_r($m);
//        
//        
//        
//        echo '</pre>';
//        $pat = "/^\/([^\/]+)\/$/";
//        $a = preg_match($pat, "/test/", $h);
//        echo '<Pre>';
//        print_r($a);
//        print_r($h);
//        exit();
        $objRecognizedRoute = null;
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
            if ($boolDebug === true) {
                echo '<pre>'.$numPregMatchResult;
                print_r($arrHits);
                echo '</pre>';
            }
            if ($numPregMatchResult === 1) {
             
//                exit();
                if (!empty($arrHits)) {
                    $arrFilteredHits = array();
                    if ($boolDebug === true) {
                        echo '<pre>';
                        print_r($arrHits);
                        echo '</pre>';
                    }
                    
//                    for ($numHit = 1; $numHit<count($arrHits); $numHit+=2) {
//                        $arrFilteredHits[] = $arrHits[$numHit];
//                    }
//                    array_shift($arrHits);
////                    Request::setParams($arrFilteredHits);
//                    
//                    Request::setParams($arrHits);
                    for ($numHit = 1; $numHit<count($arrHits); $numHit++) {
                        if (substr($arrHits[$numHit], -1) !== '/') {
                            $arrFilteredHits[] = $arrHits[$numHit];
                        }
                    }
                    Request::setParams($arrFilteredHits);
//                    echo '<pre>';
//                    print_r($arrFilteredHits);
//                    
//                    print_r($arrHits);
//                    exit();
                    
                }
                $objRecognizedRoute = $objRoute;
                Debug::log('On route '.$objRecognizedRoute->strRouteFullName, 'core-router');
                break;
            }
        }
        
//        if (empty($objRecognizedRoute)) {
//            echo "ERROR! ".__FILE__.'::'.__FUNCTION__.'#'.__LINE__;
//            exit();
//        }
        return $objRecognizedRoute;
    }
    
    
    public function getRouteByName($strRouteName) {
//        echo $strRouteName.'<pre>';
//        print_r($this->arrRoutes);
//        exit();
        if (empty($this->arrRoutes[$strRouteName])) {
            echo '<pre>';
            print_r(debug_backtrace());
            echo '</pre>';
            exit('Nie zdefiniowana ścieżka '.$strRouteName);
        } 
        return $this->arrRoutes[$strRouteName];
    }
    
    
}