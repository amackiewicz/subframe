<?php namespace webcitron\Subframe;

if (!defined('APP_DIR')) {
    define('APP_DIR', dirname(__FILE__).'/../../../../app');
}

mb_internal_encoding("UTF-8");

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
        if (Debug::isEnabled()) {
            echo Debug::top();
        }
        echo $objResponse;
        if (Debug::isEnabled()) {
            echo Debug::output();
        }
    }

}

