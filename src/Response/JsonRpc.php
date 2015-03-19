<?php namespace webcitron\Subframe\Response;


class JsonRpc extends \webcitron\Subframe\Response { 
    
    public static $objInstance = null;
    
    private function __construct()
    {
        
    }
    
    public static function getInstance()
    {
        if (self::$objInstance === null) {
            self::$objInstance = new JsonRpc();
        }
        return self::$objInstance;
    }
    
    public function render () {
        return json_encode($this->arrData);
    }
    
    public function output($strJsonRpc, $strId)
    {
        $arrResponse = array();
        $arrResponse['jsonrpc'] = $strJsonRpc;
        $arrResponse['id'] = $strId;
        $arrResponse['result'] = $this->arrData;
        echo json_encode($arrResponse);
    }
    
}