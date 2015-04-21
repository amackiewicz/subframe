<?php namespace webcitron\Subframe;

class Launcher {
    public function __get($strTrash) {
        $objSubframe = new Subframe();
        $objApp = $objSubframe->getApp();
        $objApp->init();
        if ($strTrash !== 'jsonrpc_black_magic') {
            $objResponse = $objApp->launch();
            echo $objResponse;
        }
    }
}

