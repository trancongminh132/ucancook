<?php
class My_Zend_Plugin_Block
{
	/**
	 * Zend_Config_Ini
	 * @var Zend_Config_Ini $configuration
	 */
	private static $configuration = null;
	
	/**
	 * My_Zend_Cache
	 * @var My_Zend_Cache $caching
	 */
	private static $caching = null;
	
	/**
	 * Zend_Layout instance
	 * @var Zend_Layout
	 */
	private static $_layout = null;
	
    /**
     * Constructor
     *
     */
	private final function __construct() {}
	
	/**
	 * Set instance of Block
	 *	 
	 */
	private final static function setInstance()
	{
		//Set Layout Instance		
		self::$_layout = Zend_Layout::getMvcInstance();
		
		//Get Ini Configuration
		if(is_null(self::$configuration))
		{
		    self::$configuration = new Zend_Config_Ini(APPLICATION_PATH.'/configs/block.ini', APPLICATION_ENVIRONMENT);
		    
		    //Set block configuration
		    Zend_Registry::set(BLOCK_CONFIGURATION, self::$configuration);
		}
		
		//Get caching instance
		if(is_null(self::$caching))
		{
			self::$caching =  My_Zend_Cache::getInstance(self::$configuration->caching->toArray());
		}
	}
	
	/**
	 * Clone function
	 *
	 */
	private final function __clone() {}
			
	/**
	 * Set layout
	 * @param string $layout	 
	 */
	public static function setLayout($layout)
	{
		//Check to set instance
		if(is_null(self::$_layout))
		{
			self::setInstance();
		}
		
		//Set Layout
		self::$_layout->setLayout($layout);
		
		//Set Block Helper
	    if(empty(self::$_layout->_blocks))
		{
			self::$_layout->_blocks = array();
		}
	}
	
	/**
	 * Get layout	 
	 */
	public static function getLayout()
	{
		//Check to set instance
		if(is_null(self::$_layout))
		{
			self::setInstance();
		}
		
		//Get Layout
		return self::$_layout->getLayout();
	}
	
	/**
	 * Set block with content
	 * @param string $block
	 * @param string $content
	 */
	public static function setBlock($block, $content)
	{
	    //Check to set instance
		if(is_null(self::$_layout))
		{
			self::setInstance();
		}
		
		//Set block with content
		$arr = self::$_layout->_blocks;
		$arr[$block] = $content;
		self::$_layout->_blocks = $arr;
						
		//Cleanup
		unset($arr);
	}
	
	/**
	 * Get block
	 * @param string $block
	 */
	public static function getBlock($block)
	{
		 //Check to set instance
		if(is_null(self::$_layout))
		{
			self::setInstance();
		}
		
		//Get block with content
		$arr = self::$_layout->_blocks;
		if(isset($arr[$block]))
		{
			return $arr[$block];
		}
		
		//Return default
		return '';
	}
	
	/**
	 * Get static content
	 * @param string $content
	 */
	public static function getStaticBoxContent($content)
	{
		return $content;
	}
	
	/**
	 * Get rest content
	 * @param string $url
	 */
	public static function getRestBoxContent($url)
	{		
        //Set header
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        		
        //Process curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Googlebot/2.1 (http://www.googlebot.com/bot.html)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); 
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $html= curl_exec($ch);
        
        //Return body content
        if($html)
        {
        	$html = preg_replace("/.*<body[^>]*>|<\/body>.*/si", "", $html);
	        return $html;	        
        }       
		return '';
	}
	
	/**
	 * Dynamic get content
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @param string $params
	 */
	public static function getDynamicBoxContent($module, $controller, $action, $params= array(), $expired=0)
	{
		//Check connection storage
		if(is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$content = false;
		$id = md5($module.'_'.$controller.'_'.$action.'_'.Zend_Json::encode($params));
		if($expired > 0)
		{			
			$idCaching = 'blckdmcnt_'.$id;
			$content = self::$caching->read($idCaching);
		}
		
		//if empty
		if($content === false)
		{
			//Startup View Render
			$view = new Zend_View(); 
				
			//Return content
			$content = $view->action($action, $controller, $module, $params);	
			
			//If empty
		    if(empty($content))
		    {
		    	$content = '';
		    }
		    else
		    {
			    //Set cache
			    if($expired > 0)
			    {
			    	//Check expired time
			    	if($expired < 900)
			    	{
			    		$expired = 900;
			    	}
			    	
			    	//Writing caching
			    	self::$caching->write($idCaching, $content, 0, $expired);
			    }
		    }		    
		}		
						
		//Return content
		return $content;		
	}
}