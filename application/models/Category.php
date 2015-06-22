<?php
class Category
{
	const table_category = 'category';

	// Static variable
	private static $pCategoriesAlias = null;
	private static $_category = null;
	private static $_categoryTree = null;
	const TYPE_CATEGORY_NEWS = 1;
	const TYPE_CATEGORY_INGREDIENT = 2;
	
	public static $_ARRAY_TYPE_CATEGORY = array(
		self::TYPE_CATEGORY_NEWS => 'Danh mục tin tức',
		self::TYPE_CATEGORY_INGREDIENT => 'Danh mục nguyên liệu'
	);
	
	public static function initCategory($data)
	{
		$fields = array('category_id', 'category_name', 'category_alias', 'type', 'page_title', 'meta_keyword', 'meta_description', 'parent_id', 'status', 'ordering', 'show_menu','created_date', 'updated_date');

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
	 * Insert category
	 * @param array $data
	 */
	public static function insertCategory($data)
	{
		//Init data
		$data = self::initCategory($data);

		if($data === false)
		{
			return false;
		}

		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			$table = self::table_category;
	
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
			My_Zend_Logger::log('Category::insertCategory - '. $ex->getMessage());		
			return false;
		}
	}

	/**
	 * Update category
	 * @param array $data
	 */
	public static function updateCategory($data)
	{
		try
		{
			$data = self::initCategory($data);
			 
			if($data === false || !isset($data['category_id']))
			{
				return false;
			}
	
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			$table = self::table_category;
	
			//Update data
			$rs = $storage->update($table, $data, 'category_id='. $data['category_id']);
	
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Category::updateCategory - '. $ex->getMessage());
			
			return false;
		}
	}

	/**
	 * Delete category
	 * @param int $categoryId
	 */
	public static function deleteCategory($categoryId)
	{
		try 
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			$table = self::table_category;
	
			//Delete data		
			$rs = $storage->query('DELETE FROM '.$table.' WHERE category_id = '. $categoryId);
			
			//Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Category::deleteCategory - '. $ex->getMessage());			
			return false;
		}
	}


	/**
	 * Select path of category
	 * @param int $category_id
	 */
	public static function selectSinglePath($categoryId)
	{
		if(empty($categoryId))
		{
			return false;
		}

		$sub  = self::selectCategory($categoryId);
		$main = self::selectCategory($sub['parent_id']);
		
		$data = array();
		
		if(!empty($main))
		{
			$data[] = $main;
		}

		$data[] = $sub;
		
		return $data;
	}

	/**
	 * Select detail of category
	 * @param int $category_id
	 */
	public static function selectCategory($categoryId)
	{
		if(empty($categoryId))
		{
			return false;
		}

		if(isset(self::$_category[$categoryId]))
		{
			return self::$_category[$categoryId];
		}

		$data = array();
		
		try 
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::table_category;

			//Query data from database
			$select = $storage->select()
				->from($table, '*')
				->where('category_id = ?', $categoryId)			
				->limit(1, 0);
			
			$data = $storage->fetchRow($select);
			
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Category::selectCategory - '. $ex->getMessage());
			
			return $data;
		}
		
		self::$_category[$categoryId] = $data;

		return $data;
	}
	
	/**
	 * Select detail of category by alias
	 * @param int $categoryAlias
	 */
	public static function selectCategoryByAlias($categoryAlias)
	{
		if(empty($categoryAlias))
		{
			return false;
		}
	
		if(isset(self::$_category[$categoryAlias]))
		{
			return self::$_category[$categoryAlias];
		}
	
		$category = array();
	
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();			
	
			//Query data from database
			$select = $storage->select()
							->from(self::table_category, '*')
							->where('category_alias = ?', $categoryAlias)
							->limit(1, 0);
				
			$category = $storage->fetchRow($select);
				
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Category::selectCategoryByAlias - '. $ex->getMessage());
				
			return $category;
		}
	
		self::$_category[$categoryAlias] = $category;
	
		return $category;
	}

	public static function selectSubCategoriesAlias()
	{
		//Cache instance
		$caching = My_Zend_Globals::getCaching(self::$_cacheInstance);

		//Get cache
		$idCaching = self::prefix_caching_category_alias.'all';
		$data = $caching->read($idCaching);

		//If cache not set
		if($data === false)
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage2();
			$table = self::table_category;

			//Query data from database
			$select = $storage->select('category_id, category_alias')
			->from($table)
			//->where('is_deleted = ?', 0)
			->where('parent_id > ?', 0);
			$data = $storage->fetchAll($select);

			//If empty
			if(empty($data))
			{
				$data = array();
			} else {
				$tmp = array();
				foreach ($data as &$row)
				{
					$tmp[$row['category_id']] = $row['category_alias'];
				}
				$data = $tmp;
				unset($tmp);
			}

			//Set cache
			$caching->write($idCaching, $data);
		}
		return $data;
	}

	/**
	 *
	 * Select list category by main category
	 * @param int $parent_id
	 */
	public static function selectCategoryList($filters=array(), $offset = 0, $limit = 30)
	{
		$data = array();
		 
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::table_category;

			//Query data from database
			$select = $storage->select()
				->from($table, '*')
				->order('ordering')
				->limit($limit, $offset);						

			if(isset($filters['parent_id']) && !empty($filters['parent_id']))
			{
				$select->where('parent_id = ?', $filters['parent_id']);
			}
			
			if(isset($filters['show_menu']))
			{
				$select->where('show_menu = ?', $filters['show_menu']);
			}
			
			if(isset($filters['status']))
			{
				$select->where('status = ?', $filters['status']);
			}
			
			if(isset($filters['category_name']))
			{
				$select->where($table .'.category_name LIKE ?', '%'. $filters['category_name'] .'%');
			}
			
			if(isset($filters['type']))
			{
				$select->where('type = ?', $filters['type']);
			}
			
			$data = $storage->fetchAll($select);		
		}
		catch(Exception $ex)
		{			
			My_Zend_Logger::log('Category::selectCategoryList - '. $ex->getMessage());			
			return false;
		}
		 
		return $data;
	}

	/**
	 *
	 * count category by main category
	 * @param int $filters
	 */
	public static function countCategory($filters=array())
	{
		$data = array();
		 
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::table_category;

			//Query data from database
			$select = $storage->select()
						->from($table, 'count(*) as total');						

			if(isset($filters['parent_id']) && !empty($filters['parent_id']))
			{
				$select->where('parent_id = ?', $filters['parent_id']);
			}
			
			if(isset($filters['show_menu']))
			{
				$select->where('show_menu = ?', $filters['show_menu']);
			}
			
			if(isset($filters['status']))
			{
				$select->where('status = ?', $filters['status']);
			}
			
			if(isset($filters['category_name']))
			{
				$select->where($table .'.category_name LIKE ?', '%'. $filters['category_name'] .'%');
			}
				
			if(isset($filters['type']))
			{
				$select->where('type = ?', $filters['type']);
			}
			
			$data = $storage->fetchRow($select);

			$data = $data['total'];
		}
		catch(Exception $ex)
		{			
			My_Zend_Logger::log('Category::countCategory - '. $ex->getMessage());			
			return false;
		}
		 
		return $data;
	}
	
	/**
	 *
	 * Select list child category of category
	 * @param int $categoryId
	 */
	public static function selectChildCategoryList($categoryId)
	{
		if(empty($categoryId))
		{
			return false;
		}
		
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::table_category;

			//Query data from database
			$select = $storage->select()
						->from($table, '*')
						->where('parent_id = ?', $categoryId)
						->order('ordering asc');		
		
			$data = $storage->fetchAll($select);			
		
			return $data;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Category::selectChildCategoryList - '. $ex->getMessage());
			
			return array();
		}
	}

	public static function selectChildCategory($categoryId)
	{
		$arrCategory = self::selectChildCategoryList($categoryId);
				
		return $arrCategory;
	}

	/**
	 * Get category tree
	 */
	public static function selectCategoryTree()
	{
		if(!is_null(self::$_categoryTree))
			return self::$_categoryTree;
		
		$tree = array();
		$parentList = self::selectCategoryList(array('parent_id' => 0));
		$i = 0;
		
		if ($parentList)
		{
			foreach ($parentList as $parent)
			{
				$parent['sub'] = self::selectChildCategoryList($parent['category_id']);
				
				if(count($parent['sub'])>1)
				{
					//$parent['sub'] = array_slice($parent['sub'], 1);
				}
				
				$tree[] = $parent;
			}

		}
		return $tree;
	}

	/**
	 * Get category tree
	 */
	public static function selectMenuCategoryTree()
	{
		if(!is_null(self::$_categoryTree))
			return self::$_categoryTree;
	
		$tree = array();
		$parentList = self::selectCategoryList(array('parent_id' => 0, 'status' => 1, 'show_menu' => 1));
		$i = 0;
	
		if ($parentList)
		{
			foreach ($parentList as $parent)
			{
				$parent['sub'] = self::selectChildCategoryList($parent['category_id']);
	
				if(count($parent['sub'])>1)
				{
					//$parent['sub'] = array_slice($parent['sub'], 1);
				}
	
				$tree[] = $parent;
			}
	
		}
		return $tree;
	}	
	
	public static function categoryUrl($category, $absolute=false)
	{
		if(empty($category) || !is_array($category))
		{
			return '';
		}
		
		$url = '/'.$category['category_alias'].'.html';
		
		if($absolute)
			$url = BASE_URL . $url;
		
		return $url;
	}
	
	/**
	 *
	 *
	 * generate category news url
	 */
	public static function categoryNewsUrl($data)
	{
		if(empty($data))
		{
			return '';
		}	
		return '/blog/'.$data['category_alias'].'_'.$data['category_id'].'.html';
	}
}
