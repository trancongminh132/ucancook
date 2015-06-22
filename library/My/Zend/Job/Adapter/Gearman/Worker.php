<?php
class My_Zend_Job_Adapter_Gearman_Worker
{
   /**
	 * GearmanWorker instance
	 *
	 * @var GearmanWorker
	 */
	private $worker = null;
	
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
		$this->worker = new GearmanWorker();
		$this->worker->addServer($options['host'], $options['port']);
	}
	
	/**
     * Destructor
     *
     */
	public function __destruct()
	{				
		//Cleanup
		unset($this->worker);
	}
	
	/**
	 * Add function to worker
	 * @param string $register_function
	 * @param string $callback_function
	 * @param var $args
	 */
	public function addFunction($register_function, $callback_function, $args=null)
	{
		$this->worker->addFunction($register_function, $callback_function, $args);
	}
	
	/**
	 * Run worker
	 */
	public function run()
	{
	    print "Waiting for job...\n";
		while($this->worker->work())
		{
		  if($this->worker->returnCode() != GEARMAN_SUCCESS)
		  {
		      echo "return_code: " . $this->worker->returnCode() . "\n";		     
		  }
		}
	}
	
	/**
	 * Get Notify Data in worker
	 * @param GearmanJob $job
	 */
	public function getNotifyData($job)
	{
        return unserialize($job->workload());
	}
}