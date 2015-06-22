<?php
class My_Zend_Cache_Adapter_Memcache extends My_Zend_Cache_Adapter_Abstract
{
    /**
	 * Memcache object
	 *
	 * @var object
	 */
	private $memcached = null;
			
	/**
     * Constructor
     *
     */
    public function __construct($options)
    {
    	//Get memcache option
    	$mem_options = $options['memcache'];
    	
    	//Check host
    	if(empty($mem_options['host']))
    	{
    		throw new My_Zend_Cache_Exception('Input Host for Memcache.');
    	}
    	
    	//Check tcp port
        if(empty($mem_options['tcp_port']))
    	{
    		throw new My_Zend_Cache_Exception('Input TCP Port for Memcache.');
    	}
    	
        //Check udp port
        if(empty($mem_options['udp_port']))
    	{
    		$mem_options['udp_port'] = 0;
    	}
    	
        //Check persistent
        if(empty($mem_options['persistent']))
    	{
    		$mem_options['persistent'] = false;
    	}
    	
        //Check weight
        if(empty($mem_options['weight']))
    	{
    		$mem_options['weight'] = 1;
    	}
    	
        //Check timeout
        if(empty($mem_options['timeout']))
    	{
    		$mem_options['timeout'] = 10;
    	}
    	
        //Check retry_interval
        if(empty($mem_options['retry_interval']))
    	{
    		$mem_options['retry_interval'] = 15;
    	}
    	
        //Check retry_interval
        if(empty($mem_options['status']))
    	{
    		$mem_options['status'] = false;
    	}
    	        
        //Set debug
        $this->setDebug($options['debug']); 

        //Set log
        $this->setLog($options['log']);
        
        //Set compression
        $this->setCompression($mem_options['compression']);

        //If log mode
        if($this->log)
        {
        	//Check logger directory
    		if(empty($options['logger']['path']))
    		{
    			throw new My_Zend_Cache_Exception('Input Logdir for Memcache.');
    		}
    		
    		//Get logger
    		$logger = new My_Zend_Logger($options['logger']['path']);
    		
    		//Set logger
    		$this->setLogger($logger);
        }
        
        //If debug mode
    	if($this->debug)
    	{
    		//Get Profiler
    		$profiler = My_Zend_Cache_Profiler::getInstance($options['profiler']);

    		//Set Profiler
    		$this->setProfiler($profiler);    		
    	}
    	
    	//Create instance memcached
    	$this->memcached = new MemcachePool();
    	
    	//Add server
    	$this->memcached->addServer(
    	    $mem_options['host'],
    	    $mem_options['tcp_port'],
    	    $mem_options['udp_port'],
    	    $mem_options['persistent'],
    	    $mem_options['weight'],
    	    $mem_options['timeout'],
    	    $mem_options['retry_interval'],
    	    $mem_options['status']
    	);

    	//Cleanup
    	unset($mem_options);
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        //Close memcache
		if($this->memcached) 
		{
            $this->memcached->close();
        }
        
        //Cleanup
        unset($this->memcached);
    }
        
    /**
    * Get single cache
    *
    * @param string $key   
    * @return var
    */
	public function read($key)
	{
		//If empty
    	if(empty($key))
    	{
    		return false;
    	}
    	
        //Time start
        if($this->debug)
        {
        	$start = gettimeofday(true);
        }  

        //Get data       
        $data = $this->memcached->get($key);
        
        //Check hit
        if($data === false)
        {   
        	//Write log
        	if($this->log)
        	{        		
				$this->logger->log("Miss key Single:($key)");
        	}
        	
        	//Write Profiler
        	if($this->debug)
        	{				
        		$this->profiler->addTotalMissesCache($key, 1);
        	}        	
        }
        else 
        {    
        	//Write log
        	if($this->log)
        	{        		
				$this->logger->log("Hit key Single:($key)");
        	}
        	
        	//Write Profiler
        	if($this->debug)
        	{				
        		$this->profiler->addTotalHitsCache($key, 1);
        	}        	  
        }
                
        //Add time ellapsed
        if($this->debug)
        {       
        	//Write Profiler 	
        	$end = gettimeofday(true);
        	$this->profiler->addTotalEllapsedTime($key, ($end - $start));
        	$this->profiler->addDataCache($key, $data);
        }
        
        return $data;
	}
	
	/**
    * Get List cache
    *
    * @param array $arrKeys
    * Example : array('key1', 'key2')   
    * @return var
    */
	public function readMulti($arrKeys)
	{
		//If empty
    	if(empty($arrKeys))
    	{
    		return false;
    	}
    	
    	//Check array
    	if(!is_array($arrKeys))
    	{
    		return false ;
    	}
    	
    	//Time start
        if($this->debug)
        {
        	$start = gettimeofday(true);
        	$listkey= implode(' ,', $arrKeys);
        }    	    	           
                    
        //Get data 
        $data = $this->memcached->get($arrKeys);  
       
        //Check hit
        if(empty($data))
        {  
            //Write log
        	if($this->log)
        	{        		
				$this->logger->log("Miss key List:($listkey)");
        	}
        	
        	//Write Profiler
        	if($this->debug)
        	{				
        		$this->profiler->addTotalMissesCache($listkey, 1);
        	}        	     	
        }
        else 
        {     
            //Write log
        	if($this->log)
        	{        		
				$this->logger->log("Hit key List:($listkey)");
        	}
        	
        	//Write Profiler
        	if($this->debug)
        	{				
        		$this->profiler->addTotalHitsCache($listkey, 1);
        	}        	
        }
                        
        //Add time ellapsed
        if($this->debug)
        {
        	//Write Profiler 
        	$end = gettimeofday(true);	
        	$this->profiler->addTotalEllapsedTime($listkey, ($end - $start));
        	$this->profiler->addDataCache($listkey, $data);
        }
        
        return $data;
	}
	
	/**
     * Write single cache
     *     
     * @param string $key
     * @param var $data     
     * @param int $expire
     * @return boolean
     */
    public function write($key, $data, $flag=0, $expire=0)
    {
    	//Set flag
    	$flag = $this->compression;
    	
    	//Write log   	                       
        if($this->log)
    	{
    		$this->logger->log("Write key ($key) width data (".serialize($data).")");
    	}
    	    
        return $this->memcached->set($key, $data, $flag, $expire);
    }
    
    /**
     * Write list cache
     * @param array $arrKeys
     * Example : array('key1'=>'value1', 'key2'=>'value2')
     * @param int $flag
     * @param int $expire
     */
    public function writeMulti($arrKeys, $flag=0, $expire=0)
    {
    	//Set flag
    	$flag = $this->compression;
    	
        //If empty
    	if(empty($arrKeys))
    	{
    		return false;
    	}
    	
    	//Check array
    	if(!is_array($arrKeys))
    	{
    		return false ;
    	}
    	
    	//List keys
    	$listkey= implode(' ,', array_keys($arrKeys));
    	
    	//Write log   	                       
        if($this->log)
    	{
    		$this->logger->log("Write key ($listkey) width data (".serialize(array_values($arrKeys)).")");
    	}
    	    
        return $this->memcached->set($arrKeys, null, $flag, $expire);
    }

    /**
     * Delete single cache
     *
     * @param string $key
     * @param int $timeout
     * @return boolean
     */
    public function delete($key, $timeout=0)
    {
        //Write log   	                       
        if($this->log)
    	{
    		$this->logger->log("Delete key ($key)");
    	}
    	                
        return $this->memcached->delete($key, $timeout);	
    }
    
    /**
     * Delete list cache
     * @param array $arrKeys
     * Example : array('key1', 'key2')
     * @param int $timeout
     */
    public function deleteMulti($arrKeys, $timeout=0)
    {
    	//If empty
    	if(empty($arrKeys))
    	{
    		return false;
    	}
    	
    	//Check array
    	if(!is_array($arrKeys))
    	{
    		return false ;
    	}
    	
    	//List keys
    	$listkey= implode(' ,', $arrKeys);
    	
    	//Write log   	                       
        if($this->log)
    	{
    		$this->logger->log("Delete list keys ($listkey)");
    	}
    	                
        return $this->memcached->delete($arrKeys, $timeout);
    }
    
    /**
     * Increment single
     * @param string $key
     * @param int $value
     */
    public function increment($key, $value=1)
    {
        //Write log   	                       
        if($this->log)
    	{
    		$this->logger->log("Increment key ($key)");
    	}
    	                
        return $this->memcached->increment($key, $value);
    }
    
    /**
     * Increment multiple
     * @param array $arrKeys
     * Example : array('key1', 'key2')
     * @param int $value
     */
    public function incrementMulti($arrKeys, $value=1)
    {
    	//If empty
    	if(empty($arrKeys))
    	{
    		return false;
    	}
    	
    	//Check array
    	if(!is_array($arrKeys))
    	{
    		return false ;
    	}
    	
    	//List keys
    	$listkey= implode(' ,', $arrKeys);
    	
    	//Write log   	                       
        if($this->log)
    	{
    		$this->logger->log("Increment list keys ($listkey)");
    	}
    	                
        return $this->memcached->increment($arrKeys, $value);
    }
    
    /**
     * Decrement single
     * @param string $key
     * @param int $value
     */
    public function decrement($key, $value=1)
    {
        //Write log   	                       
        if($this->log)
    	{
    		$this->logger->log("Decrement key ($key)");
    	}
    	                
        return $this->memcached->decrement($key, $value);
    }
    
    /**
     * Decrement multiple
     * @param array $arrKeys
     * Example : array('key1', 'key2')
     * @param int $value
     */
    public function decrementMulti($arrKeys, $value=1)
    {
    	//If empty
    	if(empty($arrKeys))
    	{
    		return false;
    	}
    	
    	//Check array
    	if(!is_array($arrKeys))
    	{
    		return false ;
    	}
    	
    	//List keys
    	$listkey= implode(' ,', $arrKeys);
    	
    	//Write log   	                       
        if($this->log)
    	{
    		$this->logger->log("Decrement list keys ($listkey)");
    	}
    	                
        return $this->memcached->decrement($arrKeys, $value);
    }
}