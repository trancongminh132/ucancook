<?php
class Coupon
{
	const _TABLE = 'coupon';
	
	/**
	 * init
	 * @param unknown $data
	 * @return multitype:unknown
	*/
	public static function init($data)
	{
		$fields = array('id', 'coupon_name', 'price', 'value', 'status', 'created_date', 'updated_date');

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
			My_Zend_Logger::log('Coupon::insert - '. $ex->getMessage());
			return false;
		}
	}

	/**
	 * Update Coupon
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
			My_Zend_Logger::log('Coupon::update - '. $ex->getMessage());
				
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
			My_Zend_Logger::log('Coupon::delete - '. $ex->getMessage());
			return false;
		}
	}

	/**
	 * Select detail of Coupon
	 * @param int $id
	 */
	public static function getCoupon($id)
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
			My_Zend_Logger::log('Coupon::getCoupon - '. $ex->getMessage());
			return $data;
		}

		return $data;
	}

	/**
	 *
	 * Select list Coupon
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
						->order('value asc')
						->limit($limit, $offset);
			
			if(isset($filters['status']))
			{
				$select->where('status = ?', $filters['status']);
			}
			
			$data = $storage->fetchAll($select);
				
			return $data;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Coupon::getList - '. $ex->getMessage());
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
			My_Zend_Logger::log('Coupon::countTotal - '. $ex->getMessage());
			return false;
		}
			
		return $data;
	}
}
