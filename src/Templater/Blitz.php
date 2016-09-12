<?php namespace webcitron\Subframe\Templater;

use webcitron\Subframe\Application;
use webcitron\Subframe\Languages;

class Blitz
{
    private static $objInstance = null;
    private $objBlitz = null;
    private $arrMetaData = array();

    private function __construct()
    {
        $this->objBlitz = new SubBlitz();
    }
    
    public function parse ($strTemplateFilePath, $arrData = array()) {
        return $this->objBlitz->include($strTemplateFilePath, $arrData);
    }
    
    public static function getInstance() {
        if (self::$objInstance === null) {
            self::$objInstance = new Blitz();
        }
        return self::$objInstance;
    }
    
    
    public function getTemplateFileContent ($strFilePath, $arrViewData = array()) {
         $strTemplateFileContent = $this->objBlitz->include($strFilePath.'.blitz.tpl', $arrViewData);
        
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
    
    public static function escapeQuotes ($strInput) {
        return htmlspecialchars($strInput, ENT_QUOTES);
    }
    
    public static function getCurrentLanguage ($strReplace = '') {
        $objLanguages = Languages::getInstance();
        
        $strLanguage = $objLanguages->getCurrentLanguage();
        if (!empty($strReplace)) {
            $strLanguage = str_replace('_', $strReplace, $strLanguage);
        }
        
        return $strLanguage;
    }
    
    public static function __ () {
        // $strKey, $numVariation = 1, $arrVariables ...
        $arrParams = func_get_args();
        $strKey = array_shift($arrParams); // param 1
        
        if (count($arrParams) === 0) {
            $strTranslation = Languages::__($strKey);
        } else {
            $numVariety = array_shift($arrParams); // param 2
            $arrVariables = $arrParams; // other patams
            $strTranslation = Languages::__($strKey, $arrVariables, $numVariety);
        }
        
        return $strTranslation;
//        $objLanguages = Languages::getInstance();
//        $objLanguages->getCurrentLanguagePhraseTranslation($strKey, $arrVariables, $numVariation-1);
//        echo '<Pre>';
//        print_r($objLanguages);
//        exit();
//        return $strKey;
    }
    
    /**
     * @return string
     *  Find route based on its name and return its uri (with parameteres)
     */
    public static function url()
    {
        // dynamic parameteres :(
        $arrParams = func_get_args();
        $strRouteName = array_shift($arrParams);
        $objLanguages = Languages::getInstance();
        return \webcitron\Subframe\Url::route($strRouteName, $arrParams, $objLanguages->getCurrentLanguage());
    }
    
    public static function currentUrl () {
        $objCurrentRoute = \webcitron\Subframe\Router::getCurrentRoute();
        $arrRequestParams = \webcitron\Subframe\Request::getParams();
        $strUrl = \webcitron\Subframe\Url::route($objCurrentRoute->strRouteFullName, $arrRequestParams);
        return $strUrl;
    }
    
    public static function plaintext ($strInput) {
        $strString = htmlspecialchars(strip_tags($strInput));
        return $strString;
    }
    
    public static function html ($strInput) {
//        $strString = addslashes($strInput);
        return $strInput;
    }
    
    public static function renderUserJs ($numEnableCaching = 1) {
        $objJsController = \webcitron\Subframe\JsController::getInstance();
        $objApplication = Application::getInstance();
        $strUserJs = $objJsController->render($objApplication->strName, $numEnableCaching);
        return $strUserJs;
    }
    
    public static function cssControllerRender ($boolIsAsync = false) {
        $objCssController = \webcitron\Subframe\CssController::getInstance();
        $objApplication = Application::getInstance();
        if ($boolIsAsync === true) {
            $strCssTags = $objCssController->renderAsync($objApplication->strName);
        } else {
            $strCssTags = $objCssController->render($objApplication->strName);
        }
        return $strCssTags;
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
    
    public static function prettyDateTime ($mulDateTime) {
        $objLanguages = Languages::getInstance();
        $strCurrentlanguage = $objLanguages->getCurrentLanguage();
        
        if (empty($mulDateTime)) {
            if ($strCurrentlanguage === 'pl_PL') {
                return '<i>nie określono</i>';
            } else {
                return '<i>unknown</i>';
            }
        } else if (intval($mulDateTime) === $mulDateTime) {
            $numTimestamp = $mulDateTime;
        } else {
            $numTimestamp = strtotime($mulDateTime);
        }
        $numNow = time();
        $strReturn = '';
        
//        echo $numTimestamp .'('.date('Y-m-d H:i:s', $numTimestamp).') '.$numNow.'('.date('Y-m-d H:i:s', $numNow).')<br />';
        if ($numTimestamp >= $numNow - (60*15)) {
            if ($strCurrentlanguage === 'pl_PL') {
                $strReturn = 'przed chwilą';
            } else {
                $strReturn = 'just now';
            }
        } else if ($numTimestamp >= $numNow - (60*30)) {
            if ($strCurrentlanguage === 'pl_PL') {
                $strReturn = 'pół godziny temu';
            } else {
                $strReturn = 'half hour ago';
            }
        } else if ($numTimestamp >= $numNow - (60*60)) {
            if ($strCurrentlanguage === 'pl_PL') {
                $strReturn = 'godzinę temu';
            } else {
                $strReturn = 'one hour ago';
            }
        } else if ($numTimestamp >= $numNow - (60*60*12)) {
            if ($strCurrentlanguage === 'pl_PL') {
                $strReturn = 'w ciągu ostatnich 12 godz';
            } else {
                $strReturn = 'today';
            }
        } else {
            $strReturn = self::prettyDate($mulDateTime);
        }
        
        return $strReturn;
    }
    
    public static function formatDateTime ($mulDateTime, $strFormat) {
        if (intval($mulDateTime) === $mulDateTime) {
            $numTimestamp = $mulDateTime;
        } else {
            $numTimestamp = strtotime($mulDateTime);
        }
        
        return date($strFormat, $numTimestamp);
    }
    
    public static function prettyDate ($mulDateTime) {
        $objLanguages = Languages::getInstance();
        $strCurrentLanguage = $objLanguages->getCurrentLanguage();
        
        if (intval($mulDateTime) === $mulDateTime) {
            $numTimestamp = $mulDateTime;
        } else {
            $numTimestamp = strtotime($mulDateTime);
        }
        $numNow = time();
        $strReturn = '';
        if (date('Ymd', $numTimestamp) === date('Ymd', $numNow)) {
            if ($strCurrentLanguage === 'pl_PL') {
                $strReturn = 'dzisiaj';
            } else {
                $strReturn = 'today';
            }
        } else if (date('Ymd', $numTimestamp) === date('Ymd', $numNow-(60*60*24))) {
            if ($strCurrentLanguage === 'pl_PL') {
                $strReturn = 'wczoraj';
            } else {
                $strReturn = 'yesterday';
            }
        } else {
            $strReturn = date('d.m.Y', $numTimestamp);
        }
        return $strReturn;
    }
    
    public function pagination ($strPaginationName, $boolExtended = true) {
        $objPagination = \backend\classes\Pagination::get($strPaginationName);
        return $objPagination->render($boolExtended);
    }
    
    
    public function currentEnvironment () {
        return Application::currentEnvironment();
    }
    
    public function makeGrid ($arrItems, $arrViewLayout, $arrAdverts = array(), $numPerPage = 0) {
        $strHtml = '';
        if (!empty($arrItems)) {  
            $strCurrentLanguage = Languages::getInstance()->getCurrentLanguage();
            $strAddedWord = ($strCurrentLanguage === 'pl_PL') ? 'dodano' : 'added';
            $strSeeWord = ($strCurrentLanguage === 'pl_PL') ? 'Zobacz' : 'Details';
            
            $strHtml .= '<div class="stream-row">';
            $arrConfig = array();
            foreach ($arrViewLayout as $arrViewRow) {
                $arrConfigRow = array();
                foreach ($arrViewRow as $arrViewCell) {
                    $numColNormal = $arrViewCell[0];
                    $numColMobile = $arrViewCell[1];
                    $arrConfigRow[] = sprintf('col-md-%d col-sm-%d', $numColNormal, $numColMobile);
                }
                $arrConfig[] = array(
                    count($arrConfigRow), 
                    $arrConfigRow
                );
            }
            $numRowConfigIndex = 0;
            $numItemInRowIndex = 0;
            $numItemListingIndex = 0;

            $strTempalatePath = dirname(__FILE__).'/../../../../../app/imagehost3/box/artifact/view/GridItemTemplate.blitz.tpl';
            
            // put advert
//            echo count($arrItems);
//            exit();
            if (!empty($arrAdverts)) {
                foreach ($arrAdverts as $numAdvertNo => $strAdvert) {
                    $numRandFrom = $numAdvertNo * $numPerPage;
                    $numRandTo = $numRandFrom + $numPerPage;
                    $numInsertBefore = rand($numRandFrom, $numRandTo);
                    array_splice($arrItems, $numInsertBefore, 0, array(array('isAdvert' => true, 'code' => $strAdvert)));
//                    echo $numAdvertNo .' -> '.$strAdvert.'<br />';
//                    exit();
                }
//                exit();
//                $numInsertBefore = rand(0, count($arrItems)-1);
//                array_splice($arrItems, $numInsertBefore, 0, array(array('isAdvert' => true, 'code' => $arrAdverts[0])));
            }
//            
            foreach ($arrItems as $arrItem) {
                if ($numItemInRowIndex === count($arrConfig[$numRowConfigIndex][1])) {
                    // change row
                    $numRowConfigIndex++;
                    if ($numRowConfigIndex === count($arrConfig)) {
                        $numRowConfigIndex = 0;
                    }
                    $numItemInRowIndex = 0;
                    $strHtml .= '</div><div class="row stream-row">';
                }
                
                $strCellClasses = $arrConfig[$numRowConfigIndex][1][$numItemInRowIndex];
                
                if (isset($arrItem['isAdvert']) && $arrItem['isAdvert'] === true) {
                    $strCell = $arrItem['code'];
                } else {
                    $arrItem['title'] = ucfirst(stripslashes($arrItem['title']));
                    $arrItem['description'] = stripslashes($arrItem['description']);
                    $arrItem['strAlt'] = strip_tags(trim($arrItem['title'], '. ').'. '.trim($arrItem['description']));
                    $arrItem['added_word'] = $strAddedWord;
                    $arrItem['see_word'] = $strSeeWord;
                    if (strlen($arrItem['strAlt']) > 200) {
                        $arrItem['strAlt'] = mb_substr($arrItem['strAlt'], 0, 200, 'UTF-8').'...';
                    }
                    $strCell = $this->include($strTempalatePath, $arrItem);
                }
                if ($numItemListingIndex+1 > $numPerPage) {
                    $strCellClasses .= ' loaded-buffor';
                }
                $strHtml .= '<div class="item-wrapper '.$strCellClasses.'">'.$strCell.'</div>';
                $numItemInRowIndex++;
                $numItemListingIndex++;
            }

            $strHtml .= '</div>';
        }
        
        return $strHtml;
    }
    
}
