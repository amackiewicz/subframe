<?php namespace webcitron\Subframe\Templater;

use webcitron\Subframe\Application;

class Blitz
{
    private static $objInstance = null;
    private $objBlitz = null;
    private $arrMetaData = array();

    private function __construct()
    {
        $this->objBlitz = new SubBlitz();
    }
    
    public static function getInstance() {
        if (self::$objInstance === null) {
            self::$objInstance = new Blitz();
        }
        return self::$objInstance;
    }
    
    public function getTemplateFileContent ($strFilePath, $arrViewData = array()) {
         $strTemplateFileContent = $this->objBlitz->include($strFilePath.'.tpl', $arrViewData);
        
        return $strTemplateFileContent;
    }
    
    // -----
    
    public function renderResponseView($objCurrentRoute, $strLayoutName, $strViewName, $arrViewData, $arrMetaData = array()) {
        $this->arrMetaData = $arrMetaData;
        $arrLayoutData = array_merge(
            $arrViewData, 
            array(
                'strController' => $objCurrentRoute->strControllerName, 
                'strAction' => $objCurrentRoute->strActionName
            )
        );
        $strLayout = $this->objBlitz->include(APP_DIR.'/layouts/'.$strLayoutName.'.tpl', $arrLayoutData);
        $strView = $this->objBlitz->include(APP_DIR.'/views/'.$strViewName.'.tpl', $arrViewData);

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

class SubBlitz extends \Blitz implements \webcitron\Subframe\ITemplaterHelper {
    
    
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
    
    public static function renderUserJs () {
        $objJsController = \webcitron\Subframe\JsController::getInstance();
        $objApplication = Application::getInstance();
        $strUserJs = $objJsController->render($objApplication->strName);
        return $strUserJs;
    }
    
    public static function renderHeadAddons () {
        $objJsController = \webcitron\Subframe\JsController::getInstance();
        $objApplication = Application::getInstance();
        $strHeadAddons = $objJsController->render($objApplication->strName);
        return $strHeadAddons;
    }
    
    public static function metaData($strKey, $strWrapper = '', $boolNeedEscaping = true) {
        $objTemplaterBlitz = Blitz::getInstance();
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
    
    public function pagination ($strPaginationName, $boolExtended = true) {
        $objPagination = \backend\classes\Pagination::get($strPaginationName);
        return $objPagination->render($boolExtended);
    }
    
}
