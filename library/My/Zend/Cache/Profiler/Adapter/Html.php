<?php
class My_Zend_Cache_Profiler_Adapter_Html extends My_Zend_Cache_Profiler_Adapter_Abstract
{
	/**
	 * List Keys
	 * @var array
	 */
	private $_keys = array();
	
    /**
     * Constructor
     *
     */
    public function __construct($options=array())
    {
        //Check name
    	if(empty($options['name']))
    	{
    		throw new My_Zend_Cache_Exception('Input Name of Profiler.');
    	}
    	    	
    	//Get keys
    	$this->_keys = $this->getKeys($options['name']);
    	
    	//Set profiler name
    	$this->setProfilerName($options['name']);
    }
    
    /**
     * Destructor
     *
     */
	public function __destruct()
	{
		unset($this->_keys);
	}	
	
	/**
	 * Add data
	 *
	 * @param string $key
	 * @param object
	 */
	public function addDataCache($key, $data)
	{
		if(!isset($this->_keys[$key]['data']))
		{
			$this->_keys[$key]['data'] = null;
		}
	    $this->_keys[$key]['data'] = var_export($data, true);

	    //Update keys
	    $this->setKeys($this->profiler_name, $this->_keys);
	}
	
	/**
	 * addTotalMissesCache
	 * @param string $key
	 * @param int $add
	 */
	public function addTotalMissesCache($key, $add=0)
	{
		if(!isset($this->_keys[$key]['total_misses']))
		{
			$this->_keys[$key]['total_misses'] = 0;
		}
	    $this->_keys[$key]['total_misses'] += $add;	
	    
	    //Update keys
	    $this->setKeys($this->profiler_name, $this->_keys);
	}
	
	/**
	 * addTotalHitsCache
	 * @param string $key
	 * @param int $add
	 */
	public function addTotalHitsCache($key, $add=0)
	{
		if(!isset($this->_keys[$key]['total_hits']))
		{
			$this->_keys[$key]['total_hits'] = 0;
		}
	    $this->_keys[$key]['total_hits'] += $add;	

	    //Update keys
	    $this->setKeys($this->profiler_name, $this->_keys);
	}
	
	/**
	 * addTotalEllapsedTime
	 * @param string $key
	 * @param int $add
	 */
	public function addTotalEllapsedTime($key, $add=0)
	{
		if(!isset($this->_keys[$key]['total_time']))
		{
			$this->_keys[$key]['total_time'] = 0;
		}
	    $this->_keys[$key]['total_time'] += $add;
	    
	    //Update keys
	    $this->setKeys($this->profiler_name, $this->_keys);
	}
	
	/**
	 * getPercentMissesCache
	 *
	 * @param int $total_hits
	 * @param int $total_misses
	 * @return int
	 */
	public function getPercentMissesCache($total_hits, $total_misses)
	{	   
	   $total = $total_hits + $total_misses;
	   if($total == 0) return 0;
	   $percent = intval($total_misses/$total);
	   return $percent * 100; 	
	}
	
	/**
	 * getPercentHitsCache
	 *
	 * @param int $total_hits
	 * @param int $total_misses
	 * @return int
	 */
	public function getPercentHitsCache($total_hits, $total_misses)
	{	   
	   $total = $total_hits + $total_misses;
	   if($total == 0) return 0;
	   $percent = intval($total_hits/$total);
	   return $percent * 100; 	
	} 
	
	/**
	 * getProfilerData
	 *
	 * @return string
	 */
	public function getProfilerData($type='Block')
	{
		$print = '<br/><br/><br/><table border="1" cellspacing="2" cellpadding="2"><tr><th colspan="8" bgcolor=\'#dddddd\'>Caching '.$type.' Profiler</th></tr>';
		$print .= '<tr><td align="left" width="20">Number</td><td align="left" width="20">Keys</td><td align="left">Total hits</td><td align="left">Total percent hits</td><td align="left">Total misses</td><td align="left">Total percent misses</td><td align="left">Total ellapsed time</td><td align="left">Data cache</td></tr>';
		
		//Loop keys
		$number = 0;
		$keys = $this->getKeys($this->profiler_name);					
		foreach($keys as $key => $data)
		{
			if(!empty($key))
			{
				$data['total_hits']=isset($data['total_hits'])?$data['total_hits']:0;
				$data['total_misses']=isset($data['total_misses'])?$data['total_misses']:0;
				$getPercentHitsCache = $this->getPercentHitsCache($data['total_hits'], $data['total_misses']);
				$getPercentMissesCache = $this->getPercentMissesCache($data['total_hits'], $data['total_misses']);
				$print .= '<tr><td align="left" width="20">'.$number.'</td><td align="left" width="20">'.$key.'</td><td align="left">'.$data['total_hits'].'</td><td align="left">'.$getPercentHitsCache.'%</td><td align="left">'.$data['total_misses'].'</td><td align="left">'.$getPercentMissesCache.'%</td><td align="left">'.$data['total_time'].' Seconds</td><td align="left">'.$data['data'].'</td></tr>';
				$number++;
			}
		}
		
		//If number = 0
		if($number == 0)
		{
			$print .= '<tr><td align="center" colspan="8">No index any key for caching</td></tr>';
		}
		
		$print .= "</table>";
		return $print;
	}
}