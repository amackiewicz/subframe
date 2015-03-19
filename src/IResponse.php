<?php namespace webcitron\Subframe;

interface IResponse
{
    public static function getInstance();
    public function render();
    public function output();
    
}
