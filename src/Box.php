<?php namespace webcitron\Subframe;

class Box {
    
    public $boolRunJavaScript = false;
    
    final public function render ($arrViewData = array()) {
        $strBoxFullName = get_called_class();
        $arrBoxFullNameTokens = explode('\\', $strBoxFullName);
        $strBoxName = array_pop($arrBoxFullNameTokens);
        $strBoxViewDirectory = sprintf('%s/%s/view', APP_DIR, join('/', $arrBoxFullNameTokens));
        $strBoxViewPath = sprintf('%s/%s', $strBoxViewDirectory, $strBoxName);
        
        $objTemplater = Templater::createSpecifiedTemplater(Config::get('templater'));
        $strBoxContent = $objTemplater->getTemplateFileContent($strBoxViewPath, $arrViewData);
        return $strBoxContent;
    }
    
    public function runJavaScript($boolRun = true) {
        $this->boolRunJavaScript = $boolRun;
    }
    
}