<?php namespace webcitron\Subframe;

class Box {
    
    public $strBoxName;
    public $strActionName;
    public $strView;
    
    public function setView ($strView) {
        $this->strView = $strView;
    }
    
    public function render () {
        $strView = file_get_contents(APP_DIR.'/boxes/'.$this->strBoxName.'/views/'.$this->strActionName.'.'.$this->strView.'.html');
        
        $strOutput = $strView;
        return $strOutput;
        
    }
    
}