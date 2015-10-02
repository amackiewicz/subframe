<?php namespace webcitron\Subframe;


class Launcher {
    
    private $strAppName = array();
    
    public function __construct ($strAppName = '') {
        $this->strAppName = $strAppName;
    }
    
    public function __get($strTrash) {
        if ($strTrash === 'jsonrpc_black_magic') {
            $objSubframe = new Subframe();
            $objApp = $objSubframe->getApp();
            $objApp->init();
        } else if ($strTrash === 'cron_standalone_red_button') {
            $objSubframe = new Subframe($this->strAppName);
            $objApp = $objSubframe->getApp();
        } else {
            $objSubframe = new Subframe();
            echo '1'; 
            $objApp = $objSubframe->getApp();
            echo '2'; 
            $objApp->init();
            echo '3'; 
            $objResponse = $objApp->launch();
            echo '4'; 
            exit();
            echo $objResponse;
            
            if (Debug::isEnabled()) {
                echo Debug::output();
            }
        }
    }
}

