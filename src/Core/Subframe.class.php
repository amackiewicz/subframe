<?php namespace Webcitron\Subframe\Core;

class Subframe
{
    private $objApp;

    public function __construct()
    {
        $this->objApp = Application::getInstance();
    }

    public function getApp()
    {
        return $this->objApp;
    }

}
