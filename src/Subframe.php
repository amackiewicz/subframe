<?php namespace webcitron\Subframe;

class Subframe
{
    private $objApp;

    public function __construct($strAppName = '')
    {
        $this->objApp = Application::getInstance($strAppName);
    }

    public function getApp()
    {
        return $this->objApp;
    }

}
