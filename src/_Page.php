<?php namespace webcitron\Subframe;

class Page {
    
    public $strPageName = '';
    public $strLayout = '';
    private $strView = '';
    public $arrBoxes = array();
    
    public function setLayout ($strLayout) {
        $this->strLayout = $strLayout;
    }
    
    public function setView ($strView) {
        $this->strView = $strView;
    }
    
    public function addBox($strBoxId, $strViewContainer) {
        if (!isset($this->arrBoxes[$strViewContainer])) {
            $this->arrBoxes[$strViewContainer] = array();
        }
        $this->arrBoxes[$strViewContainer][] = $strBoxId;
    }
    
    public function getView() {
        return $this->strView;
    }
    
//    public function render () {
//        
//        
//        $objResponse = new Response();
//        $strLayout = file_get_contents(APP_DIR.'/layouts/'.$this->strLayout.'.html');
//        $strView = file_get_contents(APP_DIR.'/pages/'.strtolower($this->strPageName).'/'.$this->strView.'.html');
//        
//        $strOutput = $strView;
//        foreach ($this->arrBoxes as $strBoxClass => $arrBoxes) {
//            $strBoxContent = '';
//            foreach ($arrBoxes as $strBoxName) {
//                $arrBoxIdTokens = explode('.', $strBoxName);
//                $strView = array_pop($arrBoxIdTokens);
//                $strBox = array_pop($arrBoxIdTokens);
//                $strFilepath = APP_DIR.'/boxes/';
//                foreach ($arrBoxIdTokens as $strBoxIdToken) {
//                    $strFilepath .= $strBoxIdToken.'/';
//                }
//                $strFilepath .= $strBox.'/'.ucfirst($strBox).'.class.php';
//                require_once $strFilepath;
//                
//                $objBox = new $strBox;
//                $objBox->strBoxName = $strBox;
//                $objBox->strActionName = $strView;
//                
//                $objViewMethod = new ReflectionMethod($strBox, ucfirst($strView));
//                $objViewMethod->invoke($objBox);
//                
//                $objViewMethod2 = new ReflectionMethod($strBox, 'render');
//                $strBoxContent .= $objViewMethod2->invoke($objBox);
//            }
//            $strOutput = str_replace('{container:box:'.$strBoxClass.'}', $strBoxContent, $strOutput);
//        }
//        
//        $strOutput = str_replace('{container:page}', $strView, $strOutput);
//        return $strOutput;
//        
//    }
//    
}