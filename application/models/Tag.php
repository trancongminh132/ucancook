<?php
class Tag
{
	const _TABLE = 'tags';
	
	public static function initTag($data)
	{
		$fields = array('id', 'tag_name', 'tag_name_ascii', 'tag_alias');

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
	 * Insert name
	 * @param array $data
	 */
	public static function insert($data)
	{
		//Init data
		$data = self::initTag($data);

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
			My_Zend_Logger::log('Tag::insert - '. $ex->getMessage());			
			return false;
		}
	}

	/**
	 * Update Tag
	 * @param array $data
	 */
	public static function update($data)
	{
		try
		{
			$data = self::initTag($data);
			 
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
			My_Zend_Logger::log('Tag::update - '. $ex->getMessage());
			
			return false;
		}
	}

	/**
	 * Delete Tag
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
			$rs = $storage->query('DELETE FROM '. self::_TABLE .' WHERE id = '. $id);
			
			//Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Tag::delete - '. $ex->getMessage());
			
			return false;
		}
	}

	/**
	 * Select detail of Tag
	 * @param int $id
	 */
	public static function getTag($id)
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
			My_Zend_Logger::log('Tag::getTag - '. $ex->getMessage());
			
			return $data;
		}
		
		return $data;
	}
	
	/**
	 * Select detail of Tag
	 * @param string $alias
	 */
	public static function getTagByAlias($alias)
	{
		if(empty($alias))
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
			->where('tag_alias = ?', $alias)
			->limit(1, 0);
				
			$data = $storage->fetchRow($select);
				
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Tag::getTagByAlias - '. $ex->getMessage());
				
			return $data;
		}
	
		return $data;
	}

	/**
	 *
	 * Select list Tag
	 * @param array $filters
	 */
	public static function getList($filters = array(), $offset = 0, $limit = 25)
	{
		$data = array();
		 
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_TABLE;

			//Query data from database
			$select = $storage->select()
							->from($table, '*')														
							->limit($limit, $offset);
				
			if(isset($filters['tag_alias']) && $filters['tag_alias'] != '')
			{				
				$select->where('tag_alias LIKE ?', '%'. $filters['tag_alias'] .'%');				
			}

			if(isset($filters['tag_name']))
			{
				$select->where('tag_name LIKE ?', '%'. $filters['tag_name'] .'%');
				$select->orWhere('tag_name_ascii LIKE ?', '%'. $filters['tag_name'] .'%');
			}
			
			if(isset($filters['tag_id']))
			{
				if(is_array($filters['tag_id']) && !empty($filters['tag_id']))
				{
					$select->where('id IN (?)', $filters['tag_id']);
				}
				else
				{
					$select->where('id = ?', intval($filters['tag_id']));
				}
			}
				
			$data = $storage->fetchAll($select);		
		}
		catch(Exception $ex)
		{			
			My_Zend_Logger::log('Tag::getList - '. $ex->getMessage());
			
			return array();
		}
		 
		return $data;
	}

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
	
			if(isset($filters['tag_alias']) && $filters['tag_alias'] != '')
			{				
				$select->where('tag_alias LIKE ?', '%'. $filters['tag_alias'] .'%');				
			}

			if(isset($filters['tag_name']))
			{
				$select->where('tag_name LIKE ?', '%'. $filters['tag_name'] .'%');
			}
		
			if(isset($filters['tag_id']))
			{
				if(is_array($filters['tag_id']))
				{
					$select->where('tag_id IN (?)', $filters['tag_id']);
				}
				else
				{
					$select->where('tag_id = ?', intval($filters['tag_id']));
				}
			}
				
			$data = $storage->fetchRow($select);
			
			$data = $data['total'];
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Tag::countTotal - '. $ex->getMessage());
				
			return false;
		}
			
		return $data;
	}
	
	public static function tagUrl($tag, $absolute = false)
	{
		if(empty($tag) || !is_array($tag))
		{
			return '';
		}
			
		$url = '/blog/tag/'. $tag['tag_alias'] .'.html';
		
		if($absolute)
		{
			$url = BASE_URL . $url;
		}
		
		return $url; 
	}
}
