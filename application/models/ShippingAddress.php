<?php
class ShippingAddress
{    
	// const tables
	const _TABLE = 'shipping_address';

    const _DB_INSTANCE = 'mysql';    
    
 	/**
     * set config shipping address
     * @param array $data
     */    
    public static function setConfig($data)
    {
    	try
    	{
	    	if(empty($data))
	        {
	            return false;
	        }
	        
    		$userId = $data['user_id'];
    		
	        if(!$userId)
	        {
	            return false;
	        }
	       
	        $storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
			
	        $config = self::getConfig($userId);
	        
	        if(!empty($config))
	        {
	        	 unset($data['created_date']);
	        	 $rs = $storage->update(self::_TABLE, $data, 'user_id = '.$userId);
	        }
	        else 
	        {
	        	 $rs = $storage->insert(self::_TABLE, $data); 
	        }
	        
	        return $rs;
    	}
    	catch(Exception $e)
    	{
    		My_Zend_Logger::log('ShippingAddress::setConfig ::'.$e->getMessage());
    		return false;
    	}
    }
    
    public static function getConfig($userId)
    {
    	try 
    	{
	    	$userId = intval($userId);
	    	
	    	if(empty($userId))
	    	{
	    		return false;
	    	}
		    	
	    	// get storage instance
	    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
			$table = self::_TABLE;
				
	    	$select = $storage->select()
	                    ->from(self::_TABLE, '*')
	                    ->where('user_id = ?', $userId);
	
	        $config = $storage->fetchRow($select);        
	                    
	        if(empty($config))
	        {
	            $config = array();
	        }
	       	
	    	return $config;
    	}
    	catch(Exception $e)
    	{
    		My_Zend_Logger::log('ShippingAddress::getConfig -- '.$e->getMessage());
    		return false;
    	}
    }
	
}