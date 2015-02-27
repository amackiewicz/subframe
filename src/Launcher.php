<?php namespace webcitron\Subframe;

class Launcher {
    public function __get($strTrash) {
        $objSubframe = new Subframe();
        $objApp = $objSubframe->getApp();
        $objResponse = $objApp->launch();
        echo $objResponse;
    }
}

