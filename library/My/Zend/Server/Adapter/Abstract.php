<?php
abstract class My_Zend_Server_Adapter_Abstract
{
	protected $_class='';
	protected $_method='';	
	protected static $magicMethods = array(
        '__construct',
        '__destruct',
        '__get',
        '__set',
        '__call',
        '__sleep',
        '__wakeup',
        '__isset',
        '__unset',
        '__tostring',
        '__clone',
        '__set_state',
    );
    
    /**
     * Constructor
     *
     */
    public function __construct()
    {
    	$this->_class = '';
		$this->_method = '';
    }
	
    /**
     * Destructor
     *
     */
	public function __destruct()
	{
		$this->_class = '';
		$this->_method = '';
	}	
    
	/**
     * Set class to handle
     *
     * @param string $className
     */
	public function setClass($className)
	{
		$this->_class = $className;
	}
	
	/**
     * Lowercase a string
     *
     * Lowercase's a string by reference
     *
     * @param string $value
     * @param string $key
     * @return string Lower cased string
     */
	public static function lowerCase(&$value, &$key)
	{
		return $value = strtolower($value);
	}
	
	/**
	 * Handle class
	 *
	 */
	abstract protected function handle();
}