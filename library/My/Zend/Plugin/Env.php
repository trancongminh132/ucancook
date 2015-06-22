<?php
if(isset($_GET['minhtc']) && $_GET['minhtc'] == '123mua')
{
	register_shutdown_function( "fatal_handler");
}
//register_shutdown_function( "fatal_handler" );
function fatal_handler() {
	$errfile = "unknown file";
	$errstr  = "shutdown";
	$errno   = E_CORE_ERROR;
	$errline = 0;

	$error = error_get_last();

	if( $error !== NULL) {
		$errno   = $error["type"];
		$errfile = $error["file"];
		$errline = $error["line"];
		$errstr  = $error["message"];
	}

	echo 'Fatal Error::errorNo: '. $errno . ' - ErrorString: '. $errstr .' - ErrorFile: '. $errfile .' - ErrorLine: '. $errline; 
	var_dump(CURRENT_ACTION, CURRENT_CONTROLLER, CURRENT_MODULE);
	exit;

}
class My_Zend_Plugin_Env extends Zend_Controller_Plugin_Abstract
{  
    //Debug mode    
    private $_buildStaticPage = 0;
    private $_staticPageName = '';
    
    //Starttime and stoptime
    private $starttime = 0;
    private $stoptime = 0;

    //Caching object
    private $caching = null;

    //List controller not Login
    private $listControllerLogin = array();
    
    // static cache key   
	private $_module;
	private $_controller;
	private $_action;
	private $_generalConfig = null;
	private $listModuleStatic = array();
	private $listControllerStatic = array();
	private $listActionStatic = array();
	
    /**
     * Called before Zend_Controller_Front calls on the router
     * to evaluate the request against the registered routes.
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {    	 
        //Get debug
        $debug = $request->getParam('debug');
        $this->_buildStaticPage = 0;
        $this->_staticPageName = $request->getParam('static_page_name');

        if(isset($_GET['buildhome']) && $_GET['buildhome'] == 'fuck')
        {
        	$this->_buildStaticPage = 1;
        	$this->_staticPageName = 'index.html';
        }
        
        if(isset($_GET['build_cache_page']) && !empty($_GET['static_page_name']))
        {
        	$this->_buildStaticPage = 1;
        	$this->_staticPageName = $_GET['static_page_name'].'.html';
        }
        
        //Logging
        if($debug && in_array(APPLICATION_ENVIRONMENT, array('development', 'staging')))
        {        	        		       
        	define('DEBUG_MODE', true);
            $this->starttime = gettimeofday(true);
            My_Zend_Globals::dumpLogger('Called before Zend_Controller_Front calls on the router('.gettimeofday(true).')');
        }
        else        
        {
        	define('DEBUG_MODE', false);
        }
    }

    /**
     * Called after the router finishes routing the request.
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        //Logging
        if(DEBUG_MODE == true)
        {
            My_Zend_Globals::dumpLogger('Called after the router finishes routing the request('.gettimeofday(true).')');
        }        
    }

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        //Get module
        $this->_module = strtolower($request->getParam('module'));
		//Get controller
        $this->_controller = strtolower($request->getParam('controller'));
        //Get action
        $this->_action = strtolower($request->getParam('action'));
                    	        
        //If empty then set default
        $layout = 'default';
        if(empty($this->_module))
        {
            $this->_module = 'default';
        }
        
        $layout = $this->_module;
		
        $module = $this->_module;
        // get general config        
        //Set config path
        $configs_path = APPLICATION_PATH.'/configs/'.$this->_module;

        //Load configuration
        $generalConfig = new Zend_Config_Ini($configs_path.'/general.ini', APPLICATION_ENVIRONMENT);
		
		//To array config
        $this->_generalConfig = $generalConfig->toArray();

        //Get Ini Configuration
        $configuration = My_Zend_Globals::getConfiguration();                       
                
		//Setup Include Paths
        set_include_path(implode(PATH_SEPARATOR,array(
                APPLICATION_PATH.'/models',
                get_include_path()
        )));
      	
        if($this->_action !== 'login')
        {
    		//Init User
        	$this->initUser($this->_module, $request);
        }       
       
        //Layout setup               	
        $layout_instance = Zend_Layout::startMvc(
                array(
                'layout'     => 'layout',
                'layoutPath' => APPLICATION_PATH .'/layouts/'. $layout,
                'contentKey' => 'content'
                )
        );        

        //Set configuration
        $layout_instance->_general = $this->_generalConfig;
        
        //Logging
        if(DEBUG_MODE == true)
        {
            My_Zend_Globals::dumpLogger('Debug !!!'.' remote='.My_Zend_Globals::getAltIp().' url='.$_SERVER["REQUEST_URI"].' - module:'.$module.' - controller:'.$request->getControllerName().' - action:'.$request->getActionName());
        }
       	
        //Cleanup
        unset($configs_path, $general_config);
        define('CURRENT_CONTROLLER', $this->_controller);
        define('CURRENT_ACTION', $this->_action);
		define('CURRENT_MODULE', $this->_module);
        define('IMAGES_PATH', $this->_generalConfig['static']['path']);
		define('BASE_URL', $configuration->base->url);
    }

    /**
     * Called before an action is dispatched by the dispatcher.
     * This callback allows for proxy or filter behavior.
     * By altering the request and resetting its dispatched flag
     * via Zend_Controller_Request_Abstract::setDispatched(false),
     * the current action may be skipped and/or replaced.
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {	
        //Logging
        if(DEBUG_MODE == true)
        {
            My_Zend_Globals::dumpLogger('Called before an action is dispatched by the dispatcher('.gettimeofday(true).')');
        }
    }

    /**
     * Called after an action is dispatched by the dispatcher.
     * This callback allows for proxy or filter behavior.
     * By altering the request and resetting its dispatched flag
     * via Zend_Controller_Request_Abstract::setDispatched(false)),
     * a new action may be specified for dispatching.
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        /*if(!isset($_COOKIE['sync']))
        { 
          $configuration = My_Zend_Globals::getConfiguration();
          $api_passport  = $configuration->api->passport->toArray();
          $url = BASE_URL.'/user/session?redirect='.$_SERVER["REQUEST_URI"];
          $this->redirect($api_passport['ssoredir'].'?url='.urlencode($url).'&getsession=1');
        }*/
        
        //Logging
        if(DEBUG_MODE == true)
        {
            My_Zend_Globals::dumpLogger('Called after an action is dispatched by the dispatcher('.gettimeofday(true).')');
        }
    }

    /**
     * Called after Zend_Controller_Front exits its dispatch loop.
     */
    public function dispatchLoopShutdown()
    {    	
        if($this->_buildStaticPage && $this->_staticPageName != '')
        {
        	$output = $this->getResponse()->getBody();
        	//compress html
        	$output = My_Zend_Globals::stripBuffer($output);
        	$output .= "<!-- ". date('H:i:s d/m/Y') ." --> \n";
        	// set output
        	$this->getResponse()->setBody($output);
        	$this->buildStaticPage($this->_staticPageName, $output);
        }
    }
   	
    private function _getDBProfiler()
    {    	
    	$db = My_Zend_Globals::getStorage();
        $profiler = $db->getProfiler();
        
        $totalTime    = $profiler->getTotalElapsedSecs();
        $queryCount   = $profiler->getTotalNumQueries();
        $longestTime  = 0;
        $longestQuery = null;
                 
        if($queryCount > 0)
        {
        	$print = '';
        	$count = 1;
	        foreach ($profiler->getQueryProfiles() as $query) {
	        	if ($query->getElapsedSecs() > $longestTime) {
	        		$longestTime  = $query->getElapsedSecs();
	        		$longestQuery = $query->getQuery();
	        	}
	        	$print .= '<tr><td>'. $count .'</td><td>'. $query->getElapsedSecs() .'</td><td>'. $query->getQuery() .'</td></tr>';
	        	$count++;
	        }
        }
        else 
        {
        	$print = '<tr><td colspan="3" align="center">No query found</td></tr>';
        }        

        $html = '<br/><br/><br/><table border="1" cellspacing="2" cellpadding="2" width="100%"><tr><th colspan="8" bgcolor=\'#dddddd\'>DB Profiler</th></tr>';
		$html .= '<tr><td align="left" width="20">Number</td><td align="left" width="180">Elapsed time</td><td align="left">Query</td></tr>';
		$html .= $print;
		if($queryCount > 0)
        {
	        $html .= '<tr><td colspan="3">';
	        $html .= '- Executed <b>' . $queryCount . ' queries</b> in <b>' . $totalTime .' seconds</b>' . "<br/>";
	        $html .= '- Average query length: <b>' . $totalTime / $queryCount .' seconds</b>' . "<br/>";
	        $html .= '- Queries per second: <b>' . $queryCount / $totalTime . "</b><br/>";
	        $html .= '- Longest query length: <b>' . $longestTime . "</b><br/>";
	        $html .= "- Longest query: <br/><b>" . $longestQuery . "</b><br/>";
	        $html .= '</td></tr>';
        }
        return $html;
    }
    
    /**
     * Check login
     * @param $module
     * @param $request
     */
    private function checkLogin($module = 'default', $request = null)
    {    	
    	//return true;
        switch($module)
        {
            case 'default':               
                if(LOGIN_UID <= 0)
                    $this->redirect(LOGIN_URL);
                break;
            case 'adm':             
                if(LOGIN_UID <= 0)
                    //Redirect login page
                    $this->redirect(LOGIN_ADM_URL.'?redirect='.urlencode($_SERVER["REQUEST_URI"]));
                break;                                            
            default:
            	$this->redirect(LOGIN_URL);
                break;
        }
        
        return false;
    }
    
    private function setStaticCache($controller, $action)
    {
    	
    }

    /**
     * Init User Information
     * @param $module
     * @param $request
     */
    private function initUser($module = 'default', $request = null)
    {	
		//*
		$email = '';
		$displayName = '';
        $userId = 0;
        
    	if($this->_action != 'login')
        {
			
		}		

		switch($module)
        {
            case 'default':
            	$auth = Zend_Auth::getInstance();
            	$auth->setStorage(new Zend_Auth_Storage_Session('Default'));
            	 
            	if($auth->hasIdentity())
            	{
            		$identity = $auth->getIdentity();

            		$userId = $identity->user_id;    				
            	}else
    			{
    				$auth->setStorage(new Zend_Auth_Storage_Session('Adm'));
    				if($auth->hasIdentity())
    				{
    					$identity = $auth->getIdentity();    					
    					$userId = $identity->user_id;
    				}else{
    					$this->redirect(BASE_URL . '/adm/index/login');
    				}
    			}

                if($userId > 0)
                {
                    //Check user
                    $user = User::getUser($userId);

                    //Startup user
                    if(!empty($user))
                    {
                       $displayName = $user['display_name'];
                    }
                    else
                    {
                    	$userId = 0;
                    }
                }                              
                break;
            case 'adm':
                $auth = Zend_Auth::getInstance();
    			$auth->setStorage(new Zend_Auth_Storage_Session('Adm'));

    			if($auth->hasIdentity())
    			{    				
    				$identity = $auth->getIdentity();
    				    				
    				$userId = $identity->user_id;
    				$email = $identity->email;
    			}
    			else
    			{
    				$this->redirect(BASE_URL . '/adm/index/login');
    			}
    			
                $this->checkLock($module, $userId);
                
                break;        
            default:
                break;
        }	

        //*/
        define('LOGIN_DISPLAYNAME', $displayName);
        define('LOGIN_UNAME', $email);
       	define('LOGIN_UID', (int)$userId);       	   
    }

    private function checkLock($module='default', $userId)
    {
    	$userId = 1;
        switch($module)
        {
            case 'default':
                //Check empty
                if(empty($userId))
                {
                    $this->redirect(LOGIN_URL);
                }
                
                //Select user
                $user = User::selectUser($userId);

                //Check status lock
                if($user['is_locked'])
                {
                    //Redirect
                    $this->redirect(LOCKED_URL);
                }
                break;
            case 'adm':
                //Check empty
                if(empty($userId))
                {
                    $this->redirect(LOGIN_URL);
                }
                
                //Select user
                $user = Admin::getAdmin($userId);
               	
                if(empty($user) || $user['is_locked'])
                {
                  	$this->redirect(LOCKED_ADM_URL);
                }

                Zend_Registry::set(SYSTEM_USER, $user);
                define('SYSTEM_USER_ROLE', $user['role_id']);
                define('SYSTEM_USER_IP', My_Zend_Globals::getAltIp());

                break;
            default:
                break;
        }        
    }	  
    
    /**
     * Redirect
     * @param string $url
     */
    private function redirect($url)
    {
        //Hien thi thong tin content
        if( ob_get_length() != false )
        {
            ob_end_clean();
        }
        echo '<script type="text/javascript">window.parent.location = "'.$url.'";</script>';
        exit();
    }
    
    /**
     *
     * set static page to cache
     * @param $fileName
     * @param $data
     */
    private function buildStaticPage($fileName, $data)
    {
    	try
    	{
	    	$staticFolder = 'p';
	    
	    	if(isset($_GET['is_mobile_page']) && $_GET['is_mobile_page'] == 'iscnlmmobilepage')
	    	{
	    		$staticFolder = 'mp';
	    	}
	    
	    	if($fileName == 'index.html')
	    	{
	    		$cacheFile = PAGES_PATH . "/". $fileName;
	    	}
	    	else
	    	{	// write to file cache
	    		$cacheFile = PAGES_PATH."/". $fileName;
	    	}
	    	
	    	$fh = @fopen($cacheFile, 'w');
	    	@fwrite($fh, $data);
	    	
    	}catch (Exception $e)
    	{
    		My_Zend_Logger::log('Env::buildStaticPage -- '.$e->getMessage());
			return;
    	}
    }
}