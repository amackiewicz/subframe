<?php namespace webcitron\Subframe\Templater;


class Twig implements \webcitron\Subframe\ITemplaterHelper {

    private static $objInstance = null;
    private $objTwig = null;
    
    private function __construct()
    {
        \Twig_Autoloader::register();
//        echo APP_DIR;
//        exit();
        $loader = new \Twig_Loader_Filesystem(APP_DIR);
//        $loader = new \Twig_Loader_Filesystem();
//        $loader = new \Twig_Loader_Array(array(
//            'Standard.tpl' => 'Hello {{ title }} x {{ xxx }}!',
//        ));
        $this->objTwig = new \Twig_Environment($loader);
//        $template = $twig->loadTemplate('Standard.tpl');
//        echo $template->render(array('title' => 'fuck', 'go' => 'here'));
//        echo $twig->render('test-twig.tpl', array('title' => 'fuck', 'go' => 'here'));
//        echo $twig->render('Standard.tpl', array('title' => 'fuck', 'go' => 'here'));
//        exit('s');
//        $this->objInstance = new SubBlitz();
    }
    
    public function getTemplateFileContent ($strFilePath, $arrViewData = array()) {
        $strTwigTemplatePath = substr($strFilePath, strpos($strFilePath, 'app/') + 3);
//        echo APP_DIR .' - '.;exit();
        $template = $this->objTwig->loadTemplate($strTwigTemplatePath.'.tpl');
        $strTemplateFileContent = $template->render($arrViewData);
        echo $strTemplateFileContent;
        exit();
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