<?php namespace webcitron\Subframe;

//use webcitron\Subframe\Config;

class Languages
{
    const CONFIG_NAME = 'languages';
    
    public static $objInstance = null;
    
    public $objLanguages = null;
    public $strCurrentLoadedElementTranslation = '';
    public $arrPhrasesBuffor = array();
    
    private function __construct () {
        
        
    }
    
    /**
     * @return Languages
     */
    public static function getInstance()
    {
        if (self::$objInstance === null) {
            self::$objInstance = new Languages();
        }
        return self::$objInstance;
    }
    
//    
//    public static function enable () {
//        
//        $objConfig = Config::getInstance(self::CONFIG_NAME);
//        $objConfig->set('enabled', true);
//    }
    
    public static function getLanguage() {
        $objLanguages = self::getInstance();
        $strCurrent = $objLanguages->getCurrentLanguage();
        
        return $strCurrent;
    }
    
    public function getCurrentLanguageName () {
        switch ($this->strCurrentLanguage) {
            case 'pl_PL': return 'polish';
            case 'en_US': return 'english';
        }
    }
    
    public function setCurrentLanguage ($strLanguage) {
        $this->strCurrentLanguage = $strLanguage;
    }
    
    public function getCurrentLanguage () {
        return $this->strCurrentLanguage;
    }
    
    public function loadPhrases ($strElementPath) {
        $this->strCurrentLoadedElementTranslation = $strElementPath;
        
        $arrBoxFullNameTokens = explode('\\', $strElementPath);
        $strViewName = array_pop($arrBoxFullNameTokens);
        $strTranslationFile = sprintf('%s/%s/translations/%s/%s.lang', APP_DIR, join('/', $arrBoxFullNameTokens), $this->strCurrentLanguage, $strViewName);
        
        $this->arrPhrasesBuffor = $this->loadTranslationsFile($strTranslationFile);
    }
    
    public function clearLoadedPhrases () {
        $this->arrPhrasesBuffor = array();
        $this->strCurrentLoadedElementTranslation = '';
    }
    
    public static function __ ($strKey, $arrVariables = array(), $numVariety = 1) {
        $objLanguages = Languages::getInstance();
        $numVarietyIndex = $numVariety - 1;
//        $strTranslation = $objLanguages->translatePattern($strKey, $arrVariables);
        
        if (empty($objLanguages->arrPhrasesBuffor[$strKey])) {
            $strPattern = $strKey;
        } else {
            if (!isset($objLanguages->arrPhrasesBuffor[$strKey][$numVarietyIndex])) {
                $strMessage = sprintf('Message [%s/%s/%s] dont have variety no %d', $objLanguages->strCurrentLoadedElementTranslation, $objLanguages->getCurrentLanguage(), $strKey, $numVariety);
                throw new \Exception($strMessage);
            } else {
                $strPattern = $objLanguages->arrPhrasesBuffor[$strKey][$numVarietyIndex];
            }
        }
        
        $strTranslation = $objLanguages->translatePattern($strPattern, $arrVariables);
        return $strTranslation;
    }
    
    public function translatePattern ($strPattern, $arrVariables = array()) {
//        echo $strPattern.'<br />';
//        echo '<pre>';
//        print_r($arrVariables);
//        echo '</pre>';
        $strTranslation = vsprintf($strPattern, $arrVariables);
        return $strTranslation;
    }
    
    
    public function loadTranslationsFile ($strFile) {
        $arrPatterns = array();
        if (file_exists($strFile)) {
            $arrTranslationLines = file($strFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($arrTranslationLines as $strLine) {
                $arrTranslationLine = array_map('trim', str_getcsv($strLine, ';'));
                $strKey = array_shift($arrTranslationLine);
                $arrPatterns[$strKey] = $arrTranslationLine;
            }
        }
        return $arrPatterns;
    }
}
