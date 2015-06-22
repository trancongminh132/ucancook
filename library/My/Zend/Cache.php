<?php
class My_Zend_Cache
{ 
	/**
	 * List instance
	 *
	 * @var array
	 */
	private static $instances = array();
	
	/**
     * Constructor
     *
     */
	private final function __construct() {}
	
	/**
	 * Get instance of class
	 *
	 * @param string $className
	 * @return object
	 */
	public final static function getInstance($options = array())
	{
		//Check class name
		$adapter = $options['adapter'] ;
		
		//If empty className
		if(empty($adapter))
		{
			throw new My_Zend_Cache_Exception("No instance of empty class when call My_Zend_Cache");
		}
		
		//Switch to get classname
		switch(strtolower($adapter))
		{
			case 'memcache':				
				$className = 'My_Zend_Cache_Adapter_Memcache';
				$key = $adapter;
				break;
			case 'memcachev1':
				$adapter = 'My_Zend_Cache_Adapter_MemcacheV1';
				$key = md5($options['memcachev1']['host'] . $options['memcachev1']['port']);				
				break;
			default:
				throw new My_Zend_Cache_Exception("No instance of class $adapter");
				break;
		}
						
		//Put to list
		if(!isset(self::$instances[$key]))
		{
			self::$instances[$key] = new $adapter($options);
		}
		
		//Return object class
		return self::$instances[$key];
	}
	
	/**
	 * Clone function
	 *
	 */
	private final function __clone() {}
}