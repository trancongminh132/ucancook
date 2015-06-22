<?php
class My_Zend_Job_Adapter_Gearman_Client
{
	/**
	 * GearmanClient instance
	 *
	 * @var GearmanClient
	 */
	private $client = null;
	
	/**
     * Constructor
     *
     */
	public function __construct($options = array())
	{
		//Add options
		$options = $options['gearman'];
		
	    //Check host
    	if(empty($options['host']))
    	{
    		throw new My_Zend_Job_Exception('Please input Host for Gearman.');
    	}
    	
    	//Check Port
    	if(empty($options['port']))
    	{
    		throw new My_Zend_Job_Exception('Please input Port for Gearman.');
    	}    	
    			
		//Return object class
		$this->client = new GearmanClient();
		$this->client->addServer($options['host'], $options['port']);
	}
	
	/**
     * Destructor
     *
     */
	public function __destruct()
	{				
		//Cleanup
		unset($this->client);
	}	
	
	/**
	 * Run background register task to server job
	 * @param string $register_function
	 * @param array $array_data
	 * @param int $unique
	 */
	public function doBackgroundTask($register_function, $array_data, $unique = null)
	{
		//Run job background to server
		$job_handle = $this->client->doBackground($register_function, serialize($array_data), $unique);
		
		//If error
		if ($this->client->returnCode() != GEARMAN_SUCCESS)
		{
  			throw new My_Zend_Job_Exception("Add Job unsuccess",$this->client->returnCode());
		}
	
		//Return value
		return array('jobhandle'=>$job_handle);
	}
	
	/**
	 * Run background register task to server job
	 * @param string $register_function
	 * @param array $array_data
	 * @param int $unique
	 */
	public function doHighBackgroundTask($register_function, $array_data, $unique = null)
	{
		//Run job background to server
		$job_handle = $this->client->doHighBackground($register_function, serialize($array_data), $unique);
		
		//If error
		if ($this->client->returnCode() != GEARMAN_SUCCESS)
		{
  			throw new My_Zend_Job_Exception("Add Job unsuccess",$this->client->returnCode());
		}
	
		//Return value
		return array('jobhandle'=>$job_handle);
	} 
	
	/**
	 * Run background register task to server job
	 * @param string $register_function
	 * @param array $array_data
	 * @param int $unique
	 */
	public function doLowBackgroundTask($register_function, $array_data, $unique = null)
	{
		//Run job background to server
		$job_handle = $this->client->doLowBackground($register_function, serialize($array_data), $unique);
		
		//If error
		if ($this->client->returnCode() != GEARMAN_SUCCESS)
		{
  			throw new My_Zend_Job_Exception("Add Job unsuccess",$this->client->returnCode());
		}
	
		//Return value
		return array('jobhandle'=>$job_handle);
	}

	/**
	 * Run foreground register task to server job
	 * @param string $register_function
	 * @param array $array_data
	 * @param int $unique
	 */
	public function doTask($register_function, $array_data, $unique = null)
	{
		do
		{
		    //Run job background to server
			$job_handle = $this->client->do($register_function, serialize($array_data), $unique);
		
			//Check error
		    switch($this->client->returnCode())
		    {
		        case GEARMAN_WORK_DATA:		            
		            break;
		        case GEARMAN_SUCCESS:		           
		            break;
		        case GEARMAN_WORK_FAIL:
		            return array('status'=>false);
		            break;
		        case GEARMAN_WORK_STATUS:		        	           
		            return array('status'=>$this->client->doStatus());
		            break;
		        default:
		            throw new My_Zend_Job_Exception("Add Job unsuccess",$this->client->error());
		    }
		}
		while($this->client->returnCode() != GEARMAN_SUCCESS);
			
		//Return value
		return array('jobhandle'=>$job_handle);
	}
	
	/**
	 * Run foreground register task to server job
	 * @param string $register_function
	 * @param array $array_data
	 * @param int $unique
	 */
	public function doHighTask($register_function, $array_data, $unique = null)
	{
		do
		{
		    //Run job background to server
			$job_handle = $this->client->doHigh($register_function, serialize($array_data), $unique);
		
			//Check error
		    switch($this->client->returnCode())
		    {
		        case GEARMAN_WORK_DATA:		            
		            break;
		        case GEARMAN_SUCCESS:		           
		            break;
		        case GEARMAN_WORK_FAIL:
		            return array('status'=>false);
		            break;
		        case GEARMAN_WORK_STATUS:		        	           
		            return array('status'=>$this->client->doStatus());
		            break;
		        default:
		            throw new My_Zend_Job_Exception("Add Job unsuccess",$this->client->error());
		    }
		}
		while($this->client->returnCode() != GEARMAN_SUCCESS);
			
		//Return value
		return array('jobhandle'=>$job_handle);		
	} 
	
	/**
	 * Run foreground register task to server job
	 * @param string $register_function
	 * @param array $array_data
	 * @param int $unique
	 */
	public function doLowTask($register_function, $array_data, $unique = null)
	{
		do
		{
		    //Run job background to server
			$job_handle = $this->client->doLow($register_function, serialize($array_data), $unique);
		
			//Check error
		    switch($this->client->returnCode())
		    {
		        case GEARMAN_WORK_DATA:		            
		            break;
		        case GEARMAN_SUCCESS:		           
		            break;
		        case GEARMAN_WORK_FAIL:
		            return array('status'=>false);
		            break;
		        case GEARMAN_WORK_STATUS:		        	           
		            return array('status'=>$this->client->doStatus());
		            break;
		        default:
		            throw new My_Zend_Job_Exception("Add Job unsuccess",$this->client->error());
		    }
		}
		while($this->client->returnCode() != GEARMAN_SUCCESS);
			
		//Return value
		return array('jobhandle'=>$job_handle);		
	} 
}