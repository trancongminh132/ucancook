<?php
class My_Zend_Plugin_DbBlock
{
	/**
	 * Zend_Config_Ini
	 * @var Zend_Config_Ini $configuration
	 */
	private static $configuration = null;
	
	/**
	 * My_Zend_Cache
	 * @var My_Zend_Cache $caching
	 */
	private static $caching = null;
	
	/**
	 * Storage of template
	 * @var Zend_Db
	 */
	private static $storage = null;
			
    /**
     * Constructor
     *
     */
	private function __construct() {}
	
	/**
	 * Set instance of DbBlock
	 *	 
	 */
	private final static function setInstance()
	{
		//Get Ini Configuration
		if(is_null(self::$configuration))
		{
		    self::$configuration = new Zend_Config_Ini(APPLICATION_PATH.'/configs/block.ini', APPLICATION_ENVIRONMENT);
		    
		    //Set block configuration
		    Zend_Registry::set(BLOCK_CONFIGURATION, self::$configuration);
		}
		
		//Get caching instance
		if(is_null(self::$caching))
		{
			self::$caching =  My_Zend_Cache::getInstance(self::$configuration->caching->toArray());
		}
		
		//Get adapter
		$adapter = self::$configuration->storage->adapter;
		
	    //Get storage instance
		if(is_null(self::$storage))
		{
			switch(strtolower($adapter))
			{
				case 'mysql':
					//Set UTF-8 Collate and Connection
					$options_storage = self::$configuration->storage->mysql->toArray(); 
					$options_storage['params']['driver_options'] = array(
						MYSQLI_INIT_COMMAND => 'SET NAMES utf8;'
					);
					
					//Create object to Connect DB
					self::$storage = Zend_Db::factory($options_storage['adapter'],$options_storage['params']);
					
					//Changing the Fetch Mode
					self::$storage->setFetchMode(Zend_Db::FETCH_ASSOC);
					
					//Create Adapter default is Db_Table
					Zend_Db_Table::setDefaultAdapter(self::$storage);
					
					//Unclean
					unset($options_storage);					
					break;
			default:
					throw new Exception('No adapter configuration for template !!!');					
					break;
			}	
		}
	}
	
	/**
	 * Clone function
	 *
	 */
	private final function __clone() {}
	
	/**
	 * Get data of product
	 * @param int $id
	 */
	public static function getProductDetail($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckpdc_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master			
		    $select = self::$storage->select()
					                ->from('product', '*')
					                ->where('id = ?', $id)
					                ->limit(1, 0);
		    $data = self::$storage->fetchRow($select);

			//If empty
		    if(empty($data))
		    {
		    	$data = array();
		    }
		    
		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
	/**
	 * Get list layout
	 * @param int $id
	 */
	public static function getLayoutList($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckloutlst_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master			
		    $select = self::$storage->select()
					                ->from('layout', 'id')
					                ->where('product_id = ?', $id);
		    $data = self::$storage->fetchAll($select);

		    //If empty
		    if(empty($data))
		    {
		    	$data = array();
		    }
		    else
		    {
		    	//Set temp
		    	$tmp = array();
		    	foreach($data as $values)
		    	{
		    		$tmp[0][] = $values['id'];
		    		$tmp[1][] = 'blckloutdt_'.$values['id'];
		    	}
		    	
		    	//Clone Tmp
		    	$data = $tmp;
		    }
		    
		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
	/**
	 * Get layout detail
	 * @param int $id
	 */
	public static function getLayoutDetail($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckloutdt_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master			
		    $select = self::$storage->select()
					                ->from('layout', '*')
					                ->where('id = ?', $id)
					                ->limit(1, 0);
		    $data = self::$storage->fetchRow($select);
		    
			//If empty
		    if(empty($data))
		    {
		    	$data = array();
		    }

		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
	/**
	 * Get list link of product
	 * @param int $id
	 */
	public static function getLinkList($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckprdclst_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master			
		    $select = self::$storage->select()
					                ->from('link', 'id')
					                ->where('product_id = ?', $id)
					                ->where('isshow = ?', 1);
		    $data = self::$storage->fetchAll($select);

		    //If empty
		    if(empty($data))
		    {
		    	$data = array();
		    }
		    else
		    {
		    	//Set temp
		    	$tmp = array();
		    	foreach($data as $values)
		    	{
		    		$tmp[0][] = $values['id'];
		    		$tmp[1][] = 'blckprdcdt_'.$values['id'];
		    		
		    		//Update cache detail
		    		self::getLinkDetail($values['id']);
		    	}
		    	
		    	//Clone Tmp
		    	$data = $tmp;
		    }
		    
		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
	/**
	 * Get link information
	 * @param int $id
	 */
	public static function getLinkDetail($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckprdcdt_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master						
		    $select = self::$storage->select()
					                ->from('link', '*')
					                ->where('id = ?', $id)
					                ->where('isshow = ?', 1)
					                ->limit(1, 0);
		    $data = self::$storage->fetchRow($select);
		    
			//If empty data
		    if(empty($data))
		    {
		    	$data = array();
		    }

		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
	/**
	 * Get Link of product
	 * @param int $id
	 */
	public static function getLinkListCaching($id)
	{
		//Get list link
		$list = self::getLinkList($id);
		
		//If not empty
		$data = array();
		if(!empty($list))
		{
			//Load caching
			$data = self::$caching->readMulti($list[1]);
			
			//If empty
			if(empty($data))
			{								
				return array();
			}
			
			//Sort tmp
			$tmp = array();
			foreach($data as $values)
			{
				//Set temp
				$key = $values['module'].'_'.$values['controller'].'_'.$values['action'];
				$tmp[$key] = $values ;
				
				//Clone Tmp
		    	$data = $tmp;
			}
		}
		
		return $data;
	}
	
	/**
	 * Get link detail information
	 * @param int $product_id
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 */
	public static function getLinkDetailCaching($product_id, $module, $controller, $action)
	{
		//Get link list in cache
        $arr_link = self::getLinkListCaching($product_id);
        
        //If empty
        if(empty($arr_link))
        {
        	return false;
        }
        
        //Get information of link
        $key = $module.'_'.$controller.'_'.$action;
        
        return $arr_link[$key];
	}
	
    /**
	 * Get static block detail
	 * @param int $id
	 */
	public static function getStaticBlockDetail($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckstcbdt_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master			
		    $select = self::$storage->select()
					                ->from('static_block', '*')
					                ->where('id = ?', $id)					                
					                ->limit(1, 0);
		    $data = self::$storage->fetchRow($select);
		    
		    //If empty data
		    if(empty($data))
		    {
		    	$data = array();
		    }

		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
    /**
	 * Get list static block of product
	 * @param int $id
	 */
	public static function getStaticBlockList($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckstcblst_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master			
		    $select = self::$storage->select()
					                ->from('static_block', 'id')
					                ->where('product_id = ?', $id);
		    $data = self::$storage->fetchAll($select);

		    //If empty
		    if(empty($data))
		    {
		    	$data = array();		    	
		    }
		    else
		    {
		    	//Set temp
		    	$tmp = array();
		    	foreach($data as $values)
		    	{
		    		$tmp[0][] = $values['id'];
		    		$tmp[1][] = 'blckstcbdt_'.$values['id'];
		    		
		    		//Update cache detail
		    		self::getStaticBlockDetail($values['id']);
		    	}
		    	
		    	//Clone Tmp
		    	$data = $tmp;
		    }
		    
		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
    /**
	 * Get static block of product from cache
	 * @param int $id
	 */
	public static function getStaticBlockListCaching($id)
	{
		//Get list link
		$list = self::getStaticBlockList($id);
		
		//If not empty
		$data = array();
		if(!empty($list))
		{
			//Load caching
			$data = self::$caching->readMulti($list[1]);
		}
		
		return $data;
	}
	
    /**
	 * Get rest block detail
	 * @param int $id
	 */
	public static function getRestBlockDetail($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckrstbdt_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master			
		    $select = self::$storage->select()
					                ->from('rest_block', '*')
					                ->where('id = ?', $id)					                
					                ->limit(1, 0);
		    $data = self::$storage->fetchRow($select);
		    
			//If empty data
		    if(empty($data))
		    {
		    	$data = array();
		    }

		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
    /**
	 * Get list rest block of product
	 * @param int $id
	 */
	public static function getRestBlockList($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckrstblst_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master			
		    $select = self::$storage->select()
					                ->from('rest_block', 'id')
					                ->where('product_id = ?', $id);
		    $data = self::$storage->fetchAll($select);

		    //If empty
		    if(empty($data))
		    {
		    	$data = array();
		    }
		    else
		    {
		    	//Set temp
		    	$tmp = array();
		    	foreach($data as $values)
		    	{
		    		$tmp[0][] = $values['id'];
		    		$tmp[1][] = 'blckrstbdt_'.$values['id'];
		    		
		    		//Update cache detail
		    		self::getRestBlockDetail($values['id']);
		    	}
		    	
		    	//Clone Tmp
		    	$data = $tmp;
		    }
		    
		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
    /**
	 * Get rest block of product from cache
	 * @param int $id
	 */
	public static function getRestBlockListCaching($id)
	{
		//Get list link
		$list = self::getRestBlockList($id);
		
		//If not empty
		$data = array();
		if(!empty($list))
		{
			//Load caching
			$data = self::$caching->readMulti($list[1]);
		}
		
		return $data;
	}
	
    /**
	 * Get dynamic block detail
	 * @param int $id
	 */
	public static function getDynamicBlockDetail($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckdynbdt_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master			
		    $select = self::$storage->select()
					                ->from('dynamic_block', '*')
					                ->where('id = ?', $id)					                
					                ->limit(1, 0);
		    $data = self::$storage->fetchRow($select);

			//If empty data
		    if(empty($data))
		    {
		    	$data = array();
		    }
		    
		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
    /**
	 * Get list dynamic block of product
	 * @param int $id
	 */
	public static function getDynamicBlockList($id)
	{
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		//Get cache
		$idCaching = 'blckdynblst_'.$id;
		$data = self::$caching->read($idCaching);
		
		//If empty cache
		if($data === false)
		{
			//Query Lay thong tin tu database trong bang master			
		    $select = self::$storage->select()
					                ->from('dynamic_block', 'id')
					                ->where('product_id = ?', $id);
		    $data = self::$storage->fetchAll($select);

		    //If empty
		    if(empty($data))
		    {
		    	$data = array();		    	
		    }
		    else
		    {
		    	//Set temp
		    	$tmp = array();
		    	foreach($data as $values)
		    	{
		    		$tmp[0][] = $values['id'];
		    		$tmp[1][] = 'blckdynbdt_'.$values['id'];
		    		
		    		//Update cache detail
		    		self::getDynamicBlockDetail($values['id']);
		    	}
		    	
		    	//Clone Tmp
		    	$data = $tmp;
		    }
		    
		    //Set cache
		    self::$caching->write($idCaching, $data);
		}
		
		return $data;
	}
	
    /**
	 * Get dynamic block of product from cache
	 * @param int $id
	 */
	public static function getDynamicBlockListCaching($id)
	{
		//Get list link
		$list = self::getDynamicBlockList($id);
		
		//If not empty
		$data = array();
		if(!empty($list))
		{
			//Load caching
			$data = self::$caching->readMulti($list[1]);
		}
		
		return $data;
	}
	
/*===========================Chung Viet Dung========================*/
	
	/**
	 * Set static block detail
	 * @param string $act 
	 * @param array $data
	 */
	public static function setStaticBlockDetail($act, $data) {
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		switch ($act) {    		
    		case "i": 
    			self::$storage->insert('static_block', $data);
    			// return the last value generated by an auto-increment column
				$id = self::$storage->lastInsertId();
				
				if ($id) {					
				    //Delete cache of static block list
    				$idCaching = 'blckstcblst_'.$data['product_id'];
					self::$caching->delete($idCaching);
									    				    	    				    
				    $rs = $id;
				}				
														    			
    			break;    			
    		case "u":
    			$rs = self::$storage->update('static_block', $data, 'id='.$data['id']);
    			 			
    			if ($rs) {
    				
					//Set cache of static block detail
					$idCaching = 'blckstcbdt_'.$data['id'];					
		    		self::$caching->write($idCaching, $data);
    			}
    			    			    			
    			break;
    	}   	
		return $rs;
	}	

	/**
	 * Set rest block detail
	 * @param string $act
	 * @param array $data
	 */
	public static function setRestBlockDetail($act, $data) {
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		switch ($act) {    		
    		case "i": 
    			self::$storage->insert('rest_block', $data);
    			// return the last value generated by an auto-increment column
				$id = self::$storage->lastInsertId();
				
				if ($id) {					
				    //Delete cache of rest block list					    			    
    				$idCaching = 'blckrstblst_'.$data['product_id'];
					self::$caching->delete($idCaching);
									    				    	    				    
				    $rs = $id;
				}				
														    			
    			break;    			
    		case "u":
    			$rs = self::$storage->update('rest_block', $data, 'id='.$data['id']);
    			 			
    			if ($rs) {
    				
					//Set cache of rest block detail					
					$idCaching = 'blckrstbdt_'.$data['id'];					
		    		self::$caching->write($idCaching, $data);
    			}
    			    			    			
    			break;
    	}   	
		return $rs;
	}	
	
	/**
	 * Set dynamic block detail
	 * @param string $act
	 * @param array $data
	 */
	public static function setDynamicBlockDetail($act, $data) {
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		switch ($act) {    		
    		case "i": 
    			self::$storage->insert('dynamic_block', $data);
    			// return the last value generated by an auto-increment column
				$id = self::$storage->lastInsertId();
				
				if ($id) {					
				    //Delete cache of dynamic block list					    			    
    				$idCaching = 'blckdynblst_'.$data['product_id'];
					self::$caching->delete($idCaching);
									    				    	    				    
				    $rs = $id;
				}				
														    			
    			break;    			
    		case "u":
    			$rs = self::$storage->update('dynamic_block', $data, 'id='.$data['id']);
    			 			
    			if ($rs) {
    				
					//Set cache of dynamic block detail					
					$idCaching = 'blckdynbdt_'.$data['id'];					
		    		self::$caching->write($idCaching, $data);
    			}
    			    			    			
    			break;
    	}   	
		return $rs;
	}	
	
	/**
	 * Set link detail
	 * @param string $act
	 * @param array $data
	 */
	public static function setLinkDetail($act, $data) {
		//Check connection storage
		if(is_null(self::$storage)|| is_null(self::$caching))
		{
			self::setInstance();
		}
		
		switch ($act) {    		
    		case "i": 
    			self::$storage->insert('link', $data);
    			// return the last value generated by an auto-increment column
				$id = self::$storage->lastInsertId();
				
				if ($id) {					
				    //Delete cache of link list					    			    
    				$idCaching = 'blckprdclst_'.$data['product_id'];
					self::$caching->delete($idCaching);
									    				    	    				    
				    $rs = $id;
				}				
														    			
    			break;    			
    		case "u":
    			$rs = self::$storage->update('link', $data, 'id='.$data['id']);
    			 			
    			if ($rs) {
    				
					//Set cache of link detail					
					$idCaching = 'blckprdcdt_'.$data['id'];					
		    		self::$caching->write($idCaching, $data);
    			}
    			    			    			
    			break;
    	}   	
		return $rs;
	}	
}