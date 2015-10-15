<?php namespace webcitron\Subframe\Templater;

use webcitron\Subframe\Application;

class Twig implements \webcitron\Subframe\ITemplaterHelper {

    private static $objInstance = null;
    private $objTwig = null;
    
    private function __construct()
    {
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(APP_DIR);
        $this->objTwig = new \Twig_Environment($loader);
        
        
        $objFunction = new \Twig_SimpleFunction('url', function () {
            // dynamic parameteres :(
            $arrParams = func_get_args();
            $strRouteName = array_shift($arrParams);
            return \webcitron\Subframe\Url::route($strRouteName, $arrParams);
        });
        $this->objTwig->addFunction($objFunction);
        
        
        
        $objFunction = new \Twig_SimpleFunction('plaintext', function ($strInput) {
            $strString = htmlspecialchars(strip_tags($strInput));
            return $strString;
        });
        $this->objTwig->addFunction($objFunction);
        
        
        $objFunction = new \Twig_SimpleFunction('html', function ($strInput) {
    //        $strString = addslashes($strInput);
            return $strInput;
        });
        $this->objTwig->addFunction($objFunction);
        
        
        
        $objFunction = new \Twig_SimpleFunction('renderUserJs', function () {
//            return 't<strong>es</strong>t';
            $objJsController = \webcitron\Subframe\JsController::getInstance();
            $objApplication = Application::getInstance();
            $strUserJs = $objJsController->render($objApplication->strName);
            return $strUserJs;
        }, array('is_safe' => array('html')));
        $this->objTwig->addFunction($objFunction);
        
        
        $objFunction = new \Twig_SimpleFunction('renderHeadAddons', function () {
            $objJsController = \webcitron\Subframe\JsController::getInstance();
            $objApplication = Application::getInstance();
            $strHeadAddons = $objJsController->render($objApplication->strName);
            return $strHeadAddons;
        }, array('is_safe' => array('html')));
        $this->objTwig->addFunction($objFunction);
        
        
        
        $objFunction = new \Twig_SimpleFunction('metaData', function ($strKey, $strWrapper = '', $boolNeedEscaping = true) {
//            $objTemplaterBlitz = Blitz::getInstance();
//            $strReturn = $objTemplaterBlitz->getMetaData($strKey);
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
        });
        $this->objTwig->addFunction($objFunction);
        
        
        
        $objFunction = new \Twig_SimpleFunction('baseUrl', function () {
            $strBaseUrl = Application::url();
            return $strBaseUrl;
        });
        $this->objTwig->addFunction($objFunction);
        
        
        
        $objFunction = new \Twig_SimpleFunction('prettyDateTime', function ($mulDateTime) {
            if (empty($mulDateTime)) {
                return '<i>nie określono</i>';
            } else if (intval($mulDateTime) === $mulDateTime) {
                $numTimestamp = $mulDateTime;
            } else {
                $numTimestamp = strtotime($mulDateTime);
            }
            $numNow = time();
            $strReturn = '';

            if ($numTimestamp >= $numNow - (60*15)) {
                $strReturn = 'przed chwilą';
            } else if ($numTimestamp >= $numNow - (60*30)) {
                $strReturn = 'pół godziny temu';
            } else if ($numTimestamp >= $numNow - (60*60)) {
                $strReturn = 'godzinę temu';
            } else if ($numTimestamp >= $numNow - (60*60*12)) {
                $strReturn = 'w ciągu ostatnich 12 godz';
            } else {
                $strReturn = self::prettyDate($mulDateTime);
            }

            return $strReturn;
        });
        $this->objTwig->addFunction($objFunction);
        
        
        $objFunction = new \Twig_SimpleFunction('prettyDate', function ($mulDateTime) {
        if (intval($mulDateTime) === $mulDateTime) {
                $numTimestamp = $mulDateTime;
            } else {
                $numTimestamp = strtotime($mulDateTime);
            }
            $numNow = time();
            $strReturn = '';
            if (date('Ymd', $numTimestamp) === date('Ymd', $numNow)) {
                $strReturn = 'dzisiaj';
            } else if (date('Ymd', $numTimestamp) === date('Ymd', $numNow-(60*60*24))) {
                $strReturn = 'wczoraj';
            } else {
                $strReturn = date('d.m.Y', $numTimestamp);
            }
            return $strReturn;
        });
        $this->objTwig->addFunction($objFunction);
        
        
        $objFunction = new \Twig_SimpleFunction('pagination', function ($strPaginationName, $boolExtended = true) {
            $objPagination = \backend\classes\Pagination::get($strPaginationName);
            return $objPagination->render($boolExtended);
        }, array('is_safe' => array('html')));
        $this->objTwig->addFunction($objFunction);
        
        
        $objFunction = new \Twig_SimpleFunction('currentEnvironment', function () {
            return Application::currentEnvironment();
        });
        $this->objTwig->addFunction($objFunction);
        
        
        $objFunction = new \Twig_SimpleFunction('makeGrid', function ($arrItems) {
            $strHtml = '';
            if (!empty($arrItems)) {
                $strHtml .= '<div class="row stream-row">';
                $arrConfig = array();
                $arrConfig[] = array(3, array('col-md-6 col-sm-4', 'col-md-3 col-sm-4', 'col-md-3 col-sm-4'));
                $arrConfig[] = array(3, array('col-md-4 col-sm-6', 'col-md-4 col-sm-3', 'col-md-4 col-sm-3'));
                $arrConfig[] = array(4, array('col-md-2 col-sm-3', 'col-md-3 col-sm-3', 'col-md-5 col-sm-3', 'col-md-2 col-sm-3'));

                $numRowConfigIndex = 0;
                $numItemInRowIndex = 0;

                $strTempalatePath = dirname(__FILE__).'/../../../../../app/imagehost2/box/artifact/view/GridItemTemplate.twig.tpl';
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
                    $strCell = $this->include($strTempalatePath, $arrItem);
                    $strHtml .= '<div class="item-wrapper '.$strCellClasses.'">'.$strCell.'</div>';
                    $numItemInRowIndex++;
                }

                $strHtml .= '</div>';
            }

            return $strHtml;
        }, array('is_safe' => array('html')));
        $this->objTwig->addFunction($objFunction);
        
    }
    
    public function getTemplateFileContent ($strFilePath, $arrViewData = array()) {
        $strTwigTemplatePath = substr($strFilePath, strpos($strFilePath, 'app/') + 3);
//        echo APP_DIR .' - '.;exit();
        $template = $this->objTwig->loadTemplate($strTwigTemplatePath.'.twig.tpl');
        $strTemplateFileContent = $template->render($arrViewData);
        return $strTemplateFileContent;
//        exit();
//        $arrFilePathTokens = explode('/', $strFilePath);
//        $strFileBaseName = array_pop($arrFilePathTokens);
//        $strFileDirectory = join('/', $arrFilePathTokens);
//         $strTemplateFileContent = $this->objBlitz->include($strFilePath.'.tpl', $arrViewData);
//         
//        
//        return $strTemplateFileContent;
    }
    
    public static function getInstance() {
        if (self::$objInstance === null) {
            self::$objInstance = new Twig();
        }
        return self::$objInstance;
    }
    
    public static function url() {
        
    }

}