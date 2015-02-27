<?php namespace webcitron\Subframe\Response;


class Image extends \webcitron\Subframe\Response{
    
    public static $objInstance = null;
    
    private function __construct()
    {
        
    }
    
    public static function getInstance()
    {
        if (self::$objInstance === null) {
            self::$objInstance = new Image();
        }
        return self::$objInstance;
    }
    
    
    public function output()
    {
        header("Status: 200 OK");
        header("Content-Type: image/jpeg");
        header("Content-Length: " . strlen($this->arrData['strImageContents']));
        echo $this->arrData['strImageContents'];
        
    }
    
}