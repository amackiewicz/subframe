<?php namespace webcitron\Subframe;

define('APP_DIR', dirname(__FILE__).'/../../../../app');

class Launcher {
    
    private $strVirtualDomain = '';
    
    public function __construct ($strVirtualDomain = '') {
        $this->strVirtualDomain = $strVirtualDomain;
    }
    
    public function cron () {
        $objRequest = Request::getInstance();
        $objRequest->setVirtualDomain($this->strVirtualDomain);
        
        $objSubframe = new Subframe();
        $objApp = $objSubframe->getApp();
        $objApp->init();
    }
    
    public function rpc () {
        $objSubframe = new Subframe();
        $objApp = $objSubframe->getApp();
        $objApp->init();
    }
    
    public function goBabyGo () {
        $objSubframe = new Subframe();
        $objApp = $objSubframe->getApp();
        $objApp->init();
        $objResponse = $objApp->launch();
        echo $objResponse;
        if (Debug::isEnabled()) {
            echo Debug::output();
        }
    }
//    
//    public function __get($strTrash) {
//        if ($strTrash === 'jsonrpc_black_magic') {
//            $objSubframe = new Subframe();
//            $objApp = $objSubframe->getApp();
//            $objApp->init();
//        } else if ($strTrash === 'cron_standalone_red_button') {
//            $objRequest = Request::getInstance();
//            $objRequest->setVirtualDomain($this->strVirtualDomain);
//            $objSubframe = new Subframe();
//            $objApp = $objSubframe->getApp();
//            $objApp->init();
//            echo 's'; exit();
//            
//        } else {
//            $objSubframe = new Subframe();
//            $objApp = $objSubframe->getApp();
//            $objApp->init();
//            $objResponse = $objApp->launch();
//            echo $objResponse;
//            
//            if (Debug::isEnabled()) {
//                echo Debug::output();
//            }
//        }
//    }
}

