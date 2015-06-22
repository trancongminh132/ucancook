<?php
class My_Zend_Session
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
	protected function __construct() {}
	
	/**
	 * Get instance of class
	 *
	 * @param string $className
	 * @return object
	 */
	public final static function getInstance($options = array())
	{
		//Check class name
		$className = $options['adapter'] ;
		
		//If empty className
		if(empty($className))
		{
			throw new My_Zend_Session_Exception("No instance of empty class when call My_Zend_Session");
		}
		
		//Switch to get classname
		switch (strtolower($className))
		{
			case 'memcachev1':				
				$className = 'My_Zend_Session_Adapter_Memcachev1';
				break;
			default:
				throw new My_Zend_Session_Exception("No instance of class $className");
				break;
		}
		
		//Put to list
		if(!isset(self::$instances[$className]))
		{
			self::$instances[$className] = new $className($options);
		}
		
		//Return object class
		return self::$instances[$className];
	}
	
	/**
	 * Clone function
	 *
	 */
	private function __clone() {}	
}