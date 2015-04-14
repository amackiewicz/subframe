<?php namespace webcitron\Subframe\Response;


class Html extends \webcitron\Subframe\Response{
    
    public $strContent = '';
    
    public function setContent($strContent) {
        $this->strContent = $strContent;
    }
    
    public function __toString() {
        return $this->strContent;
    }
    
//    public function output()
//    {
//        header("Status: 200 OK");
//        header("Content-Type: image/jpeg");
//        header("Content-Length: " . strlen($this->arrData['strImageContents']));
//        echo $this->arrData['strImageContents'];
//        
//    }
    
}