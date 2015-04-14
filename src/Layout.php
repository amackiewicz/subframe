<?php namespace webcitron\Subframe;

use webcitron\Subframe\Application;

class Layout {
    
    public $arrPlaceholderBoxes = array();
    
    final public function render () {
        $arrBoxesResponseContents = array();
        foreach ($this->arrPlaceholderBoxes as $strPlaceholderName => $arrBoxes) {
            $arrBoxesResponseContents[$strPlaceholderName] = array();
            foreach ($arrBoxes as $objBox) {
                $objBoxResponse = $objBox->launch();
                $arrBoxesResponseContents[$strPlaceholderName][] = $objBoxResponse->__toString();
            }
        }
        
        $strLayoutFullName = get_called_class();
        $arrLayoutFullNameTokens = explode('\\', $strLayoutFullName);
        $strLayoutName = array_pop($arrLayoutFullNameTokens);
        $strLayoutPath = sprintf('%s/layout/view/%s', Application::getInstance()->strDirectory, $strLayoutName);
        
        $objTemplater = Templater::createSpecifiedTemplater(Config::get('templater'));
        $strLayoutContent = $objTemplater->getTemplateFileContent($strLayoutPath);
        
        foreach ($this->arrPlaceholderBoxes as $strPlaceholderName => $arrBoxes) {
            $strLayoutContent = str_replace(
                sprintf('[placeholder:%s]', $strPlaceholderName), 
                join('', $arrBoxesResponseContents[$strPlaceholderName]), 
                $strLayoutContent
            );
        }
        
        return $strLayoutContent;
    }
    
   final public function addBoxes($strPlaceholderName, $arrBoxes) {
       $this->arrPlaceholderBoxes[$strPlaceholderName] = $arrBoxes;
   }
    
//    public static function launch($strBoardName) {
//        
//        $strBoardFullPath = sprintf('\board\%s', $strBoardName);
//        $objSpecifiedBoard = new $strBoardFullPath();
//        $objBoardMethod = new \ReflectionMethod($strBoardFullPath, 'index');
//        $arrRequestParams = Request::getParams();
//        $objResponse = $objBoardMethod->invokeArgs($objSpecifiedBoard, $arrRequestParams);
//        
//        echo '<pre>';
//        print_r($objSpecifiedBoard);
//        exit();
//    }
    
    
}