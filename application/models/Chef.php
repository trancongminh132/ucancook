<?php
class Chef
{
	const _TABLE = 'chef';
	
	/**
	 * init
	 * @param unknown $data
	 * @return multitype:unknown
	*/
	public static function init($data)
	{
		$fields = array('id', 'chef_name', 'chef_alias', 'gender', 'avatar', 'chef_description', 'created_date', 'updated_date');

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
			My_Zend_Logger::log('Chef::insert - '. $ex->getMessage());
			return false;
		}
	}

	/**
	 * Update Chef
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
			My_Zend_Logger::log('Chef::update - '. $ex->getMessage());
				
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
			My_Zend_Logger::log('Chef::delete - '. $ex->getMessage());
			return false;
		}
	}

	/**
	 * Select detail of Chef
	 * @param int $id
	 */
	public static function getChef($id)
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
			$select = $storage->select()->from($table, '*')
						->where('id = ?', $id)
						->limit(1, 0);
										
			$data = $storage->fetchRow($select);
				
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Chef::getChef - '. $ex->getMessage());
			return $data;
		}

		return $data;
	}

	/**
	 *
	 * Select list Chef
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
						->order('updated_date desc')
						->limit($limit, $offset);
			
			$data = $storage->fetchAll($select);
				
			return $data;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Chef::getList - '. $ex->getMessage());
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
			
			$data = $storage->fetchRow($select);
				
			$data = $data['total'];
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Chef::countTotal - '. $ex->getMessage());
			return false;
		}
			
		return $data;
	}
	
	public static function chefUrl($chef, $absolute = false)
	{
		if(empty($chef) || !is_array($chef))
		{
			return '';
		}
		
		$url = '/dau-bep/'.$chef['chef_alias'].'.html';
		
		if($absolute)
			$url = BASE_URL . $url;
		
		return $url;
	}
	
	/**
	 * get detail of chef by alias
	 * @param int $alias
	 */
	public static function getChefByAlias($alias)
	{
		if(empty($alias))
		{
			return false;
		}
	
		$chef = array();
	
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			//Query data from database
			$select = $storage->select()
							->from(self::_TABLE, '*')
							->where('chef_alias = ?', $alias)
							->limit(1, 0);
	
			$dish = $storage->fetchRow($select);
	
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Chef::getChefByAlias - '. $ex->getMessage());
			return $dish;
		}
		 
		return $dish;
	}
}
