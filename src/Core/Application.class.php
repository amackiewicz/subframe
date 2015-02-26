<?php namespace Webcitron\Subframe\Core;

class Application
{
    public static $objInstance = null;
    private $objRouter = null;
    private $objRequest = null;

    public static function getInstance()
    {
        if (self::$objInstance === null) {
            self::$objInstance = new Application();
        }
        return self::$objInstance;
    }

    private function __construct()
    {
        $this->loadConfig();
        $this->objRouter = Router::getInstance();
        $this->objRouter->loadRoutes();

        $this->objTemplater = Templater::createSpecifiedTemplater(Config::get('core', 'templater'));

        Request::read();
    }

    private function loadConfig()
    {
        require APP_DIR.'/config/app.php';
    }

    public function launch()
    {
        $objCurrentRoute = $this->objRouter->dispath();

        $objController = $objCurrentRoute->launch();
        $strOutput = $this->objTemplater->renderController($objController);

        $objResponse = new Response();
        $objResponse->setContent($strOutput);
        return $objResponse;
    }

}
