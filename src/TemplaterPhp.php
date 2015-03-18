<?php namespace webcitron\Subframe;

class TemplaterPhp
{
    private static $objInstance = null;
    private $objSubPhp = null;
    private $arrMetaData = array();

    private function __construct()
    {
        $this->objSubPhp = new SubPhp();
    }
    
    public static function getInstance() {
        if (self::$objInstance === null) {
            self::$objInstance = new TemplaterPhp();
        }
        return self::$objInstance;
    }
    
    public function renderResponseView($strLayoutName, $strViewName, $arrViewData, $arrMetaData = array()) {
        $this->arrMetaData = $arrMetaData;
        
        foreach ($arrViewData as $strName => $mulValue) {
            $this->{$strName} = $mulValue;
        }
        
        ob_start();
        
//        echo '<pre>';
//        print_r($arrViewData);
//        exit();
        
        include APP_DIR.'/layouts/'.$strLayoutName.'.tpl.php';
        $strLayout = ob_get_contents();
        
        ob_clean();
        
        include APP_DIR.'/views/'.$strViewName.'.tpl.php';
        $strView = ob_get_contents();
        
//        $strView = $this->objBlitz->include(APP_DIR.'/views/'.$strViewName.'.tpl.php', $arrViewData);

        $strOutput = str_replace('[[#placeholder:page#]]', $strView, $strLayout);

        return $strOutput;
    }

    public function renderController($objController)
    {
        $strLayoutName = $objController->strLayout;
        $strViewName = $objController->strView;
        $arrViewData = $objController->arrViewData;

        $strLayout = $this->objBlitz->include(APP_DIR.'/layouts/'.$strLayoutName.'.tpl', $arrViewData);
        $strView = $this->objBlitz->include(APP_DIR.'/views/'.$strViewName.'.tpl', $arrViewData);

        $strOutput = str_replace('[[#placeholder:page#]]', $strView, $strLayout);

        return $strOutput;
    }
    
    public function getMetaData($strKey) {
        $strReturn = isset($this->arrMetaData[$strKey]) ? $this->arrMetaData[$strKey] : '';
        return $strReturn;
    }

    public function render($objPage)
    {
        $strLayoutName = $objPage->strLayout;
        $strPageName = $objPage->strPageName;
        $strPageView = $objPage->getView();

        $strLayout = $this->objBlitz->include(APP_DIR.'/layouts/'.$strLayoutName.'.html');
        $strPageContent = $this->objBlitz->include(APP_DIR.'/pages/'.strtolower($strPageName).'/'.$strPageView.'.html');

        foreach ($objPage->arrBoxes as $strBoxPlaceholder => $arrBoxes) {
            $strPlaceholderContent = '';
            foreach ($arrBoxes as $strBoxName) {
                $arrBoxIdTokens = explode('.', $strBoxName);
                $strView = array_pop($arrBoxIdTokens);
                $strBox = array_pop($arrBoxIdTokens);
                $strFilepath = APP_DIR.'/boxes/';
                foreach ($arrBoxIdTokens as $strBoxIdToken) {
                    $strFilepath .= $strBoxIdToken.'/';
                }
                $strFilepath .= $strBox.'/'.ucfirst($strBox).'.class.php';
                require_once $strFilepath;

                $objBox = new $strBox;
                $objBox->strBoxName = $strBox;
                $objBox->strActionName = $strView;

                $objViewMethod = new ReflectionMethod($strBox, ucfirst($strView));
                $objViewMethod->invoke($objBox);

                $strBoxContent = $this->objBlitz->include(APP_DIR.'/boxes/'.$objBox->strBoxName.'/views/'.$objBox->strActionName.'.'.$objBox->strView.'.tpl');

                $strPlaceholderContent .= $strBoxContent;
            }

            $strPageContent = str_replace('[[#placeholder:box:'.$strBoxPlaceholder.'#]]', $strPlaceholderContent, $strPageContent);
        }
        $strOutput = str_replace('[[#placeholder:page#]]', $strPageContent, $strLayout);

        return $strOutput;
    }
    

}

class SubPhp implements \webcitron\Subframe\ITemplaterHelper {
    
    
    /**
     * @return string
     *  Find route based on its name and return its uri (with parameteres)
     */
    public static function url()
    {
        // dynamic parameteres :(
        $arrParams = func_get_args();
        $strRouteName = array_shift($arrParams);
        return \webcitron\Subframe\Url::route($strRouteName, $arrParams);
    }
    
    public static function metaData($strKey, $strWrapper = '', $boolNeedEscaping = true) {
        $objTemplaterBlitz = TemplaterBlitz::getInstance();
        $strReturn = $objTemplaterBlitz->getMetaData($strKey);
        if (!empty($strWrapper)) {
            if (empty($strReturn)) {
                $strReturn = '';
            } else {
                if ($boolNeedEscaping === true) {
                    $strReturn = htmlspecialchars($strReturn);
                }
                $strReturn = sprintf($strWrapper, $strReturn);
            }
        }
        return $strReturn;
    }
    
    public static function baseUrl() {
        $strBaseUrl = Application::url();
        return $strBaseUrl;
    }
    
}
