<?php
class My_Zend_JobWorker
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
		$className = $options['adapter'] ;
		
		//If empty className
		if(empty($className))
		{
			throw new My_Zend_Job_Exception("No instance of empty class when call My_Zend_JobWorker");
		}
		
		//Switch to get classname
		switch(strtolower($className))
		{
			case 'gearman':				
				$className = 'My_Zend_Job_Adapter_Gearman_Worker';
				break;
			default:
				throw new My_Zend_Job_Exception("No instance of class $className");
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
	private final function __clone() {}
}