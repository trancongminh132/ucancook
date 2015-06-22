<?php
class Banner
{
	const _TABLE = 'banner';
	const _TOP_BANNER_HOMEPAGE = 1;
	const _TOP_BANNER_HOMEPAGE_BLOG = 2;
	
	public static $_BANNER_POSITIONS = array(
		self::_TOP_BANNER_HOMEPAGE => 'Banner Top Trang chủ',
		self::_TOP_BANNER_HOMEPAGE_BLOG => 'Banner Top Trang chủ Blog'
	);
	/**
	 * init
	 * @param unknown $data
	 * @return multitype:unknown
	 */
	public static function init($data)
	{
		$fields = array('id', 'banner_name','link','position_id', 'ordering', 'created_date', 'updated_date', 'banner_url');
	
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
	 * Insert
	 * @param array $data
	 */
	public static function insert($data)
	{
		//Init data
		$data = self::init($data);

		if($data === false)
		{
			return false;
		}
		
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			$table = self::_TABLE;
	
			// Insert data
			$rs = $storage->insert($table, $data);
	
			if($rs)
			{
				$rs = $storage->lastInsertId();
			}
	
			// Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Banner::insert - '. $ex->getMessage());			
			return false;
		}
	}
	
	/**
	 * Update Banner
	 * @param array $data
	 */
	public static function update($data)
	{
		try
		{
			$data = self::init($data);
			 
			if($data === false || !isset($data['id']))
			{
				return false;
			}
	
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			$table = self::_TABLE;
	
			//Update data
			$rs = $storage->update($table, $data, 'id ='. $data['id']);
	
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Banner::update - '. $ex->getMessage());
			
			return false;
		}
	}

	/**
	 * Delete
	 * @param int $id
	 */
	public static function delete($id)
	{
		try 
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			$table = self::_TABLE;

			//Delete data		
			$rs = $storage->query('DELETE FROM '. $table .' WHERE id = '. $id);
			
			//Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Banner::delete - '. $ex->getMessage());			
			return false;
		}
	}
	
	/**
	 * Select detail of Banner
	 * @param int $id
	 */
	public static function getBanner($id)
	{
		if(empty($id))
		{
			return false;
		}

		$data = array();
		
		try 
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_TABLE;

			//Query data from database
			$select = $storage->select()
							->from($table, '*')
							->where('id = ?', $id)			
							->limit(1, 0);
			
			$data = $storage->fetchRow($select);
			
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Banner::getBanner - '. $ex->getMessage());			
			return $data;
		}
		
		return $data;
	}
	
	/**
	 *
	 * Select list Banner
	 * @param array $filters
	 */
	public static function getList($filters = array(), $offset = 0, $limit = 25)
	{
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_TABLE;
			
			//Query data from database
			$select = $storage->select()
							->from($table, '*')	
							->order('ordering asc')
							->limit($limit, $offset);
				
			if(isset($filters['position_id']) && !empty($filters['position_id']))
			{
				if(is_numeric($filters['position_id']))
				{
					$select->where('position_id = ?', $filters['position_id']);
				}
				elseif(is_array($filters['position_id']))
				{
					$select->where('position_id IN (?)', $filters['position_id']);
				}
			}
				
			$data = $storage->fetchAll($select);
			
			return $data;
		}
		catch(Exception $ex)
		{			
			My_Zend_Logger::log('Banner::getList - '. $ex->getMessage());			
			return array();
		}
	}

	/**
	 * 
	 * count total
	 * @param unknown $filters
	 * @return boolean|unknown
	 */
	public static function countTotal($filters = array())
	{
		$data = array();
			
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_TABLE;
	
			//Query data from database
			$select = $storage->select()
							->from($table, 'count(id) as total');
	
			if(isset($filters['position_id']) && !empty($filters['position_id']))
			{
				$select->where('position_id = ?', $filters['position_id']);
			}
				
			$data = $storage->fetchRow($select);
			
			$data = $data['total'];
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Banner::countTotal - '. $ex->getMessage());				
			return false;
		}
			
		return $data;
	}
}
