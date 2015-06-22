<?php
class My_Zend_Session_Adapter_Memcachev1 implements Zend_Session_SaveHandler_Interface
{
	/**
	 * Options of caching 
	 * @var array
	 */
	private $options = null;
	
	/**
	 * Caching instance
	 * @var object
	 */
	private $cacheInstance = null;
	
	/**
	 * Constructor
	 * @param array $options
	 */
	public function __construct($options)
	{
		//Load lifetime
		$this->lifetime = 360;
		
		//Set options
		$this->options = $options['caching'];
	}
	
	/**
	 * Reconect instance caching
	 */
	private function connect()
	{
	    //Get caching instance
		if(is_null($this->cacheInstance))
		{
			$this->cacheInstance =  My_Zend_Cache::getInstance($this->options);
		}
	}
	
	/**
	 * Set options of caching
	 * @param array $options
	 */
	private function setOption($options)
	{
		//Set options
		$this->options = $options;
	}
	
	/**
	 * Open session
	 * @param string $save_path
	 * @param string $name
	 */
	public function open($save_path, $name)
	{
		return true;
	}
	
	/**
	 * Close session
	 */
	public function close()
	{
		return true;
	}
	
	/**
	 * Read session
	 * @param string $key
	 */
	public function read($key)
	{
		//Conect
		$this->connect();
		
		//Return
		return $this->cacheInstance->read($key);	
	}
	
	/**
	 * Write session
	 * @param string $key
	 * @param string $value
	 */
	public function write($key, $value)
	{
		//Conect
		$this->connect();
		
		//Return
		return $this->cacheInstance->write($key, $value, 0, $this->lifetime);
	}
	
	/**
	 * Remove session
	 * @param string $key
	 */
	public function destroy($key)
	{
		//Conect
		$this->connect();
		
		//Return
		return $this->cacheInstance->delete($key);
	}
	
	/**
	 * garbage collector
	 * @param string $maxlifetime
	 */
	public function gc($maxlifetime)
	{
		return true;
	}
}