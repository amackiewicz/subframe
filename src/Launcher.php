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
        list($usec, $sec) = explode(" ", microtime());
        $numStart = ((float)$usec + (float)$sec);
        Debug::log('Loading start at '.$numStart, 'timing');
        $objSubframe = new Subframe();
        $objApp = $objSubframe->getApp();
        $objApp->init();
        exit();
        $objResponse = $objApp->launch();
        if (Debug::isEnabled()) {
            echo Debug::top();
        }
        echo $objResponse;
        if (Debug::isEnabled()) {
            list($usec, $sec) = explode(" ", microtime());
            $numEnd = ((float)$usec + (float)$sec);
            Debug::log('Loading end at '.$numStart, 'timing');
            Debug::log('Backend load time is '.($numEnd-$numStart).'s.', 'timing');
            echo Debug::output();
        }
    }

}

