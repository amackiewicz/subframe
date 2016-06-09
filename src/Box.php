<?php namespace webcitron\Subframe;

class Box {
    
    public $boolRunJavaScript = false;
    
    final public function render ($arrViewData = array(), $strViewName = '') {
        $strBoxFullName = get_called_class();
        $arrBoxFullNameTokens = explode('\\', $strBoxFullName);
        if (empty($strViewName)) {
            $strViewName = array_pop($arrBoxFullNameTokens);
        } else {
            array_pop($arrBoxFullNameTokens);
        }
        $strBoxViewDirectory = sprintf('%s/%s/view', APP_DIR, join('/', $arrBoxFullNameTokens));
        $strBoxViewPath = sprintf('%s/%s', $strBoxViewDirectory, $strViewName);
        
        $objApp = Application::getInstance();
        $objApp->objLanguages->loadPhrases($strBoxFullName);
        
        $objTemplater = Templater::createSpecifiedTemplater(Config::get('templater'));
        $strBoxContent = $objTemplater->getTemplateFileContent($strBoxViewPath, $arrViewData);
        
        $objApp->objLanguages->clearLoadedPhrases();
        
        return $strBoxContent;
    }
    
    public function runJavaScript($boolRun = true) {
        $this->boolRunJavaScript = $boolRun;
    }
    
}