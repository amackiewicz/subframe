<?php namespace webcitron\Subframe;

class Board {
    
    public static $objInstance = null;
    
    public static function launch($strBoardName, $strBoardMethod = 'index') {
        $strBoardFullPath = sprintf('%s\\board\\%s', Application::getInstance()->strApplicationClassesPrefix, $strBoardName);
        Debug::log('Loading board '.$strBoardFullPath.'->'.$strBoardMethod.'()', 'core-board');
        echo 'Loading board '.$strBoardFullPath.'->'.$strBoardMethod.'()';
        $objSpecifiedBoard = new $strBoardFullPath();
        echo '<pre>';
        print_r($objSpecifiedBoard);
        exit();
        $objBoardMethod = new \ReflectionMethod($strBoardFullPath, $strBoardMethod);
        $arrRequestParams = Request::getParams();
        $objResponse = $objBoardMethod->invokeArgs($objSpecifiedBoard, $arrRequestParams);
        return $objResponse;
    }
    
    
}