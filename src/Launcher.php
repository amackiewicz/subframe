<?php namespace webcitron\Subframe;

class Launcher {
    public function __get($strTrash) {
        if ($strTrash === 'jsonrpc_black_magic') {
            $objSubframe = new Subframe();
            $objApp = $objSubframe->getApp();
            $objApp->init();
        } else {
            $objSubframe = new Subframe();
            $objApp = $objSubframe->getApp();
            $objApp->init();
            $objResponse = $objApp->launch();
            echo $objResponse;
        }
    }
}

