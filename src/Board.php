<?php namespace webcitron\Subframe;

class Board {
    
    public static $objInstance = null;
    
    public static function launch($strBoardName) {
        $strBoardFullPath = sprintf('%s\board\%s', Application::getInstance()->strApplicationClassesPrefix, $strBoardName);
        
        $objSpecifiedBoard = new $strBoardFullPath();
        $objBoardMethod = new \ReflectionMethod($strBoardFullPath, 'index');
        $arrRequestParams = Request::getParams();
        $objResponse = $objBoardMethod->invokeArgs($objSpecifiedBoard, $arrRequestParams);
        return $objResponse;
    }
    
    
}