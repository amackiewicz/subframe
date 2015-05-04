<?php namespace webcitron\Subframe\Response;

use webcitron\Subframe\Request;

class Html extends \webcitron\Subframe\Response{
    
    public $strContent = '';
    
    public function setContent($strContent) {
        $this->strContent = $strContent;
    }
    
    public function __toString() {
        if ($this->numCode > 0) {
            switch ($this->numCode) {
                case 404:
                    $objRequest = Request::getInstance();
                    header(sprintf(
                        "%s 404 Not Found", 
                        $objRequest->protocol()
                    )); 
                    break;
                default:
                    exit('unexcepted response header code');
                    break;
            }
        }
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