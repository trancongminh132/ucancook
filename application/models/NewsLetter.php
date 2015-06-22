<?php
class NewsLetter
{    
	const _TABLE = 'newsletter';	
	const _DB_INSTANCE = 'mysql';    
	     
   	/**
     * Date : 9/12/2014
     * Init data
     * @param array $data
     */   
	public static function init($data)
	{	 
		$fields = array('id', 'user_id', 'email', 'ip', 'created_date');
		
		$rs = array();
		
		foreach($fields as $field)
		{
			if(isset($data[$field]))
			{
				$rs[$field]  = $data[$field];
			}
		}
		
		return $rs;
	}
	

	/**
	 * Date : 9/12/2014
	 * Insert	 
	 * @param array $data	 
	 */
		
	public static function insert($data)
	{
		try 
		{
			$data = self::init($data);
			
			if(empty($data))
			{
				return false;
			}
			
			//Get db instance
			$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	
			$table = self::_TABLE;
	
			// Insert data
			$rs = $storage->insert($table, $data);
			
			$newsLetterId = $storage->lastInsertId();
			
			// Return
			return $newsLetterId;
			
		}
		catch(Exception $ex)
    	{    		
    		My_Zend_Logger::log('NewsLetter::insert -- '. $ex->getMessage());
    		return false;
    	}  
		//Init data
		
	}

	/**
	 * Date : 9/12/2014
	 * update	 
	 * @param array $data	 
	 */	
	public static function update($data)
	{	 
		try
		{
			$data = self::init($data);
			 
			if(empty($data) || !isset($data['id']))
			{
				return false;
			}
	
			//Get db instance
			$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	
			$table = self::_TABLER;
	
			//Update data
			$rs = $storage->update($table, $data, 'id='.$data['id']);

			//Return
			return $rs;
		}catch(Exception $ex)
    	{    		
    		My_Zend_Logger::log('NewsLetter::update -- '. $ex->getMessage());
    		return 0;
    	} 
	}
	
	/**
	 * Date : 19/2/2013
     * Delete
     * @param int $newsLetterId
     */
	
    public static function delete($newsLetterId)
    {
    	$newsLetterId = intval($newsLetterId);
    	
    	if(empty($newsLetterId)|| !is_numeric($newsLetterId))
    	{
    		return false;
    	}
    	
    	try
    	{
	    	// get storage instance
	    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	    	
	    	$table = self::_TABLE;
	    	
	    	// execute
	    	$rs = $storage->delete($table, 'id ='. $newsLetterId );
	    	
	    	return $rs;
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('NewsLetter::delete -- '. $ex->getMessage());
    		return false;
    	}
    }
    
    /**
     * Date : 9/12/2014
     * Select detail
     * @param int $newsLetterId
     */
    public static function getNewsLetter($newsLetterId)
    {
        if(empty($newsLetterId) || !is_numeric($newsLetterId))
        {
            return false;
        }

        try
        {	       
	    	//Get db instance
	        $storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	            			
	        //Query data from database
	        $select = $storage->select()
	                    ->from(self::_TABLE, '*')
	                    ->where('id = ?', $newsLetterId);
	                    
	        $data = $storage->fetchRow($select);
	
	        //If empty
	        if(empty($data))
	        {
	            $data = array();
	        }	
	        
	        return $data;
        }
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('NewsLetter::getNewsLetter -- '. $ex->getMessage());
    		return false;
    	}
    }
    
   	/**
     * 
     * select list
     * @param array
     */
   	
 	public static function getList($offset = 0, $limit = 30)
    {
    	try
    	{			
	   		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	   			
	   		$table = self::_TABLE;
	   			
	   		$select = $storage->select()
	   					  ->from($table,'*')
	   					  ->limit($limit, $offset);
		    // query db
		    $listNewsLetter = $storage->fetchAll($select);
		   	
	   		return  $listNewsLetter;
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('NewsLetter::getList -- '. $ex->getMessage());
    		return array();
    	}
    }
    
	public static function isExitsEmailRegNewsLetter($email)
    {
    	try
    	{
	    	$storage = My_Zend_Globals::getStorage(1);
	    	
			$table = self::_TABLE;
			
			$query = $storage->select()
							->from($table, 'email')
							->where('email = ?', $email)
							->limit(1);	
			
			$emailRegister = $storage->fetchRow($query);
			
			if(empty($emailRegister))
				$emailRegister = array();
				
			return $emailRegister;
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('NewsLetter::isExitsEmailRegNewsLetter -- '. $ex->getMessage());
    		return array();
    	}
    }
}