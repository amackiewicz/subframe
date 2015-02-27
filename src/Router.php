<?php namespace webcitron\Subframe;

class Router {
    
    private static $objInstance = null;
    public $arrRoutes = array();
    
    private function __construct() {}
    
    public static function getInstance() {
        if (self::$objInstance === null) {
            self::$objInstance = new Router();
        }
        return self::$objInstance;
    }
    
    public function loadRoutes () {
        require APP_DIR.'/config/routes.php';
    }
    
    public function dispath () {
        $objRequest = Request::getInstance();
        $strCurrentUri = $objRequest->getUri();
        $objCurrentRoute = $this->findRoute($strCurrentUri);
        return $objCurrentRoute;
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
            $strPattern = sprintf('%s', preg_replace('/\{[^}]+\}/', '([^\/]+)', $strPattern)); 
            $strPattern = '/'.$strPattern.'/';
//            echo $strUri .' -> '.$strPattern.'<br />';
            $numPregMatchResult = @preg_match($strPattern, $strUri, $arrHits);
//            echo '<pre>'.$numPregMatchResult;
//            print_r($arrHits);
//            echo '</pre>';
            if ($numPregMatchResult === 1) {
                if (!empty($arrHits)) {
//                    echo '<pre>';
//                    print_r($arrHits);
                    array_shift($arrHits);
//                    print_r($arrHits);
//                    echo '</pre>';
//                    exit();
                    Request::setParams($arrHits);
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