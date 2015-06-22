<?php
abstract class My_Zend_Cache_Profiler_Adapter_Abstract
{		
	protected $profiler_name = 'profiler_cache';
	
	/**
	 * Set profiler name
	 * @param string $profiler_name
	 */
	protected function setProfilerName($profiler_name)
	{
		$this->profiler_name = $profiler_name;		
	}
	
	/**
	 * Set keys of profiler
	 * @param string $profiler_name
	 * @param array $keys
	 */
	protected function setKeys($profiler_name, $keys)
	{
		Zend_Registry::set($profiler_name, $keys);
	}
	
	/**
	 * Get all keys of profiler
	 * @param string $profiler_name
	 */
	protected function getKeys($profiler_name)
	{
		if(Zend_Registry::isRegistered($profiler_name))
		{
			return Zend_Registry::get($profiler_name);
		}
		
		//Set keys
		$this->setKeys($profiler_name, array());
		return array();		
	}
	
    /**
	 * Add data	 
	 * @param string $key
	 * @param object
	 */
	abstract protected function addDataCache($key, $data);
	
	/**
	 * addTotalMissesCache
	 * @param string $key
	 * @param int $add
	 */
	abstract protected function addTotalMissesCache($key, $add=0);
	
	/**
	 * addTotalHitsCache
	 * @param string $key
	 * @param int $add
	 */
	abstract protected function addTotalHitsCache($key, $add=0);
	
	/**
	 * addTotalEllapsedTime
	 * @param string $key
	 * @param int $add
	 */
	abstract protected function addTotalEllapsedTime($key, $add=0);
	
	/**
	 * getPercentMissesCache
	 *
	 * @param int $total_hits
	 * @param int $total_misses
	 * @return int
	 */
	abstract protected function getPercentMissesCache($total_hits, $total_misses);
	
	/**
	 * getPercentHitsCache
	 *
	 * @param int $total_hits
	 * @param int $total_misses
	 * @return int
	 */
	abstract protected function getPercentHitsCache($total_hits, $total_misses);
	
	/**
	 * get Profiler Data to print
	 *
	 * @return string
	 */
	abstract protected function getProfilerData();
}