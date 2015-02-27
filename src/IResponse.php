<?php namespace webcitron\Subframe;

interface IResponse
{
    public static function getInstance();
    public function output();
    
}
