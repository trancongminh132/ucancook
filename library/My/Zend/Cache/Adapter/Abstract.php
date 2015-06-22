<?php
abstract class My_Zend_Cache_Adapter_Abstract
{
	/**
	 * Logger instance
	 * @var My_Zend_Logger
	 */
	protected $logger = null;
	
	/**
	 * Flag use for debug
	 * @var boolean
	 */
	protected $debug = false;
	
	/**
	 * Flag use for log
	 * @var boolean
	 */
	protected $log = false;
	
	/**
	 * Profiler debug
	 * @var My_Zend_Cache_Profiler
	 */
	protected $profiler = null;
	
	/**
	 * Flag use for compression
	 * @var boolean
	 */
	protected $compression = false;
	
	/**
	 * Set debug
	 * @param boolean $debug
	 */
	protected function setDebug($debug)
	{
	    $this->debug = $debug;	
	}

	/**
	 * Set Log
	 * @param boolean $debug
	 */
	protected function setLog($log)
	{
	    $this->log = $log;	
	}	
	
	/**
	 * Set compression
	 * @param boolean $compression
	 */
	protected function setCompression($compression)
	{
		$this->compression = $compression;
	}
	
	/**
	 * Set Logger
	 * @param My_Zend_Logger $logger
	 */
	protected function setLogger($logger)
	{
		$this->logger = $logger;
	}
	
	/**
	 * Set Profiler
	 * @param My_Zend_Cache_Profiler $profiler
	 */
	protected function setProfiler($profiler)
	{
		$this->profiler = $profiler;
	}
	
	/**
    * Get single cache
    *
    * @param string $key   
    * @return var
    */
	abstract protected function read($key);
	
	/**
    * Get List cache
    *
    * @param array $arrKeys
    * Example : array('key1', 'key2')   
    * @return var
    */
    abstract protected function readMulti($arrKeys);
    
    /**
     * Write single cache
     *     
     * @param string $key
     * @param var $data     
     * @param int $expire
     * @return boolean
     */
    abstract protected function write($key, $data, $flag=0, $expire=0);
    
    /**
     * Write list cache
     * @param array $arrKeys
     * Example : array('key1'=>'value1', 'key2'=>'value2')
     * @param int $flag
     * @param int $expire
     */
    abstract protected function writeMulti($arrKeys, $flag=0, $expire=0);
    
    /**
     * Delete single cache
     *
     * @param string $key
     * @param int $timeout
     * @return boolean
     */
    abstract protected function delete($key, $timeout=0);

    /**
     * Delete list cache
     * @param array $arrKeys
     * Example : array('key1', 'key2')
     * @param int $timeout
     */
    abstract protected function deleteMulti($arrKeys, $timeout=0);
    
    /**
     * Increment single
     * @param string $key
     * @param int $value
     */
    abstract protected function increment($key, $value=1);
    
    /**
     * Increment multiple
     * @param array $arrKeys
     * Example : array('key1', 'key2')
     * @param int $value
     */
    abstract protected function incrementMulti($arrKeys, $value=1);
    
    /**
     * Decrement single
     * @param string $key
     * @param int $value
     */
    abstract protected function decrement($key, $value=1);
    
    /**
     * Decrement multiple
     * @param array $arrKeys
     * Example : array('key1', 'key2')
     * @param int $value
     */
    abstract protected function decrementMulti($arrKeys, $value=1);
}