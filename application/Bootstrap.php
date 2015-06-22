<?php

require_once 'Zend/Loader/Autoloader.php';

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * Front Controller
     */
    private static $front = null;
    /**
     * Application configuration
     */
    private static $configuration = null;   
    /**
     * Admin log configuration
     */
    private static $admin_log_configuration = null;
       
    /**
     * Registration of my name space
     */
    protected function initNamespace() {
        Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
    }

    /**
     * Init application configuration
     */
    protected function initAppConfiguration() 
    {
        if (null === self::$configuration) {        	        	        								        	        	

        	$configs = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENVIRONMENT);
			
        	self::$configuration = $configs;
        }

        Zend_Registry::set(APPLICATION_CONFIGURATION, self::$configuration);
    }
	
    /**
     * Logger setup
     */
    protected function initLogger() {
        //Logging system
        $logger = new Zend_Log();
        $logger->addWriter(new Zend_Log_Writer_Stream(self::$configuration->logger->dump->path));
        Zend_Registry::set(LOGGER_DUMP, $logger);
    }

    /**
     * Routers setup
     */
    protected function initRouters() 
	{
		$config = Zend_Registry::get(APPLICATION_CONFIGURATION);    	 
		
		self::$front->addControllerDirectory(APPLICATION_PATH . '/modules/default/controllers', 'default');
		self::$front->addControllerDirectory(APPLICATION_PATH . '/modules/adm/controllers', 'adm');
		
		//Default module        
		$default = new Zend_Controller_Router_Route(':controller/:action/*', array('controller' => 'index', 'action' => 'index', 'module' => 'default'));							
		//Admin module        
		$admin_router = new Zend_Controller_Router_Route('adm/:controller/:action/*', array('controller' => 'index', 'action' => 'index', 'module' => 'adm'));
		
		$loadIni = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routes');
		
		//Add router
		$routers = self::$front->getRouter();
		
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
		
		$loadIni = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routes');
			
		$routers->addRoute('default', $default);
						
		// Add From ini
		$routers->addConfig($loadIni, 'routes');        		
		$routers->addRoute('adm', $admin_router);				
		
        //Set new router
        self::$front->setRouter($routers);

        //Cleanup
        unset($routers, $default_router, $admin_router);
    }

    /**
     * Helper controller setup
     */
    protected function initControllerHelper() {
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/default/controllers/helpers');
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/modules/adm/controllers/helpers');        
    }

    /**
     * Zend_Front_Controller created on each request
     */
    protected function initFrontController() {
        //Init frontController
        self::$front = Zend_Controller_Front::getInstance();

        //Set the current environment
        self::$front->setParam(ENVIRONMENT, APPLICATION_ENVIRONMENT);

        //Enable error controller plugin
        if (APPLICATION_ENVIRONMENT == 'development') {
            self::$front->throwExceptions(true);
        } else {
            self::$front->throwExceptions(false);
        }

        //Set new router
        $this->initRouters();

        //Controller helpers
        $this->initControllerHelper();
    }

    /**
     * Plugin Front Init
     */
    protected function initFrontPlugin() {
        //Add Env Plugin
        self::$front->registerPlugin(new My_Zend_Plugin_Env());
        //self::$front->registerPlugin(new My_Zend_Plugin_SEO());
        self::$front->registerPlugin(new My_Zend_Plugin_Module());
    }

    /**
     * Run the application
     *
     * Checks to see that we have a default controller directory. If not, an
     * exception is thrown.
     *
     * If so, it registers the bootstrap with the 'bootstrap' parameter of
     * the front controller, and dispatches the front controller.
     *
     * @return void
     * @throws Zend_Exception
     */
    public function run() 
    {
        //Init namspace
        $this->initNamespace();

        //Init Application Configuration
        $this->initAppConfiguration();

        //Init Front Controller
        $this->initFrontController();

        //Check default module
        if (null === self::$front->getControllerDirectory(self::$front->getDefaultModule())) {
            throw new Zend_Exception('No default controller directory registered with front controller');
        }
        
        //Set controller plugin
        $this->initFrontPlugin();
       	
        My_Zend_Globals::setDocType('XHTML1_TRANSITIONAL');
        My_Zend_Globals::setTitle('Ucancook.vn | Chuyên cung cấp nguyên liệu nấu ăn, thực phẩm chất lượng, an toàn vệ sinh thực phẩm trên thị trường.');
        My_Zend_Globals::setMeta('keywords', 'món ngon mỗi ngày, nấu ăn, dạy nấu ăn, hướng dẫn nấu ăn, công thức nấu ăn, cẩm nang nấu ăn, mon ngon moi ngay, nau an, day nau an, huong dan nau an, cong thuc nau an, cam nang nau an');
        My_Zend_Globals::setMeta('description', 'Ucancook.vn | Cung cấp các công thức chế biến món ăn, các nguyên liệu để chế biến 1 món ăn, nguyên liệu chất lượng, đạt chuẩn vệ sinh an toàn thực phẩm, giá hấp dẫn.');
		
        //Dispatch controller		
        self::$front->setParam('bootstrap', $this);
        self::$front->dispatch();
        
        exit(0);
    }

}