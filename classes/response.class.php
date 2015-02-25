<?php
class Response {
    
    public $strContent;
    
    public function setContent($strContent) {
        $this->strContent = $strContent;
    }
    
    public function __toString()
    {
        return $this->strContent;
    }
    
}