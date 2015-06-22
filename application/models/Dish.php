<?php
class Dish
{    
    const prefix_caching_dish = 'dish.';
    const prefix_caching_dish_list = 'dish.lst.';
    const prefix_caching_total_dish = 'dish.total';

	// const tables
	const _TABLE = 'dishes';
	const _TABLE_DISH_INGREDIENT = 'dish_ingredient';
	const _TABLE_USER_TASTE = 'user_taste';
    const _DB_INSTANCE = 'mysql';   
    const _CACHE_INSTANCE = 0;
    
    private static $_dish = null;
    
    public static $_ARRAY_TYPE = array(
    	1 => array(
    		'name' => 'Món chay',
    	),
    	2 => array(
    		'name' => 'Món hải sản'
    	),
    	3 => array(
    		'name' => 'Món thịt'
    	),
    	4 => array(
    		'name' => 'Tráng miệng'
    	)
    );
    
    public static $_ARRAY_TYPE_MAIN = array(
   		1 => array(
    		'name' => 'Món chay',
    	),
    	2 => array(
    		'name' => 'Món hải sản'
    	),
    	3 => array(
    		'name' => 'Món thịt'
    	)
    );
    
    public static $_ARRAY_TASTE = array(
    	1 => array(
    		'id' => 1,
    		'name' => 'Thịt bò',
    		'image' => 'm1.jpg'
    	),
    	2 => array(
    		'id' => 2,
    		'name' => 'Hạnh nhân',
    		'image' => 'm2.jpg'
    	),
    	3 => array(
    		'id' => 3,
    		'name' => 'Cá',
    		'image' => 'm5.jpg'
    	),
    	4 => array(
    		'id' => 4,
    		'name' => 'Đậu phộng',
    		'image' => 'm3.jpg'
    	),
    	5 => array(
    		'id' => 5,
    		'name' => 'Rau củ',
    		'image' => 'm4.png'
    	),
    	6 => array(
    		'id' => 6,
    		'name' => 'Bơ sữa',
    		'image' => 'm7.jpg'
    	),
    	7 => array(
    		'id' => 7,
    		'name' => 'Bột',
    		'image' => 'm6.jpg'
    	),
    	8 => array(
    		'id' => 8,
    		'name' => 'Mì ống',
    		'image' => 'm8.jpg'
    	),
    	9 => array(
    		'id' => 9,
    		'name' => 'Thịt heo',
    		'image' => 'm9.jpg'
    	),
    	10 => array(
    		'id' => 10,
    		'name' => 'Đậu tương',
    		'image' => 'm10.jpg'
    	),
    	11 => array(
    		'id' => 11,
    		'name' => 'Tôm, cua, sò, hến',
    		'image' => 'm11.jpg'
    	),
    	12 => array(
    		'id' => 12,
    		'name' => 'Thịt gà',
    		'image' => 'm12.jpg'
    	),
    	13 => array(
    		'id' => 13,
    		'name' => 'Ớt',
    		'image' => 'm13.jpg'
    	)
    );
    
    /**
     * Init data
     * @param array $data
     */
    public static function init($data)
    {        
        $fields = array('id', 'name', 'alias', 'type', 'status', 'chef_id', 'price', 'special_price', 'status', 'attributes', 'ingredient', 'image', 'multi_image', 'description', 'created_date', 'updated_date');
		
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
     * Init data of dish ingredient
     * @param array $data
     */
    public static function initDishIngredient($data)
    {
    	$fields = array( 'dish_id', 'ingredient_id');
    
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

        if(empty($data))
        {
            return false;
        }
		
        //Get db instance
        $storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
      
        $table = self::_TABLE;
		
        // Insert data
        $rs = $storage->insert($table, $data);
        
        $dishId = $storage->lastInsertId();
       
        return $dishId;
    }
    
	/**
     * update
     * @param array $data
     */
    public static function update($data)
    {
        $data = self::init($data);

        if(empty($data) || !isset($data['id']))
        {
            return false;
        }

        //Get db instance
        $storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);

        //Get partition
        $table = self::_TABLE;

      	$rs = $storage->update($table, $data, 'id='. $data['id']);
      
        //Return
        return $rs;
    }
	/**
     * delete
     * @param array $dishId
     */
   
    public static function delete($dishId)
    {
    	$dishId = intval($dishId);
  
    	if(empty($dishId))
    	{
    		return false;
    	}
    	
    	// get storage instance
    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    	
    	$table = self::_TABLE;
    	
    	// execute
    	$rs = $storage->delete($table, 'id ='. $dishId );
    	
    	return $rs;
    }
    
    public static function getDish($dishId)
    {
    	try
    	{
	    	$dishId = intval($dishId);
	    	
	    	if(empty($dishId))
	    	{
	    		return false;
	    	}
	
	    	// get storage instance
	    	$storage = My_Zend_Globals::getStorage();
			$table = self::_TABLE;
	    	$select = $storage->select()
	                    ->from(self::_TABLE, '*')
	                    ->where('id = ?', $dishId);
			
	        $dish = $storage->fetchRow($select);        
	                    
	        if(empty($dish))
	        {
	            $dish = array();
	        }
	         
	    	return $dish;
    	}catch(Exception $e)
    	{	
    		My_Zend_Logger::log('Dish::getDish -- '.$e->getMessage());
    		return;
    	}
    }
	
   	/**
     * 
     * get List
     * @param array $filters
     */
	public static function getListDish($filters, $offset = 0, $limit = 30)
   	{
   		try
   		{		
	   		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	   		
	   		$table = self::_TABLE;
	   		//Query data from database
	    	$select = $storage->select()
	    				  ->from($table, '*')
	    				  ->limit($limit, $offset);
	    			  
			if(isset($filters['type']) && !empty($filters['type']))
			{
				$select->where('type = ?', $filters['type']);
			}
			
			if(isset($filters['chef_id']) && !empty($filters['chef_id']))
			{
				$select->where('chef_id = ?', $filters['chef_id']);
			}
			
			if(isset($filters['dish_name']) && !empty($filters['dish_name']))
			{
				$select->where($table .'.name LIKE ?', '%'. $filters['dish_name'] .'%');
			}
			
			if(isset($filters['status']))
			{
				$select->where('status = ?', $filters['status']);
			}
			
			if(isset($filters['dish_id']) && !empty($filters['dish_id']))
			{
				if(is_numeric($filters['dish_id']))
				{
					$select->where($table .'.id = ?', $filters['dish_id']);
				}
				elseif(is_array($filters['dish_id']))
				{
					$select->where($table .'.id IN (?)', $filters['dish_id']);
				}
			}
			
	    	// query db
	    	$list = $storage->fetchAll($select);
	    	
	   		return  $list;
   		}
   		catch(Exception $e)
   		{
   			My_Zend_Logger::log('Dish::getListDish -- '.$e->getMessage());
			return false;
   		}   		
   	}
	
	/**
    * 
    * Get total 
	* @param array $filters
    */
    public static function getTotalDish($filters)
    {      	
    	// get storage instance
    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    		
		$table = self::_TABLE;
			
    	$select = $storage->select()
						->from($table, 'count(id) as total');
        
		if(isset($filters['type']) && !empty($filters['type']))
		{
			$select->where('type = ?', $filters['type']);
		}
		
		if(isset($filters['chef_id']) && !empty($filters['chef_id']))
		{
			$select->where('chef_id = ?', $filters['chef_id']);
		}
		
		if(isset($filters['dish_name']) && !empty($filters['dish_name']))
		{
			$select->where($table .'.name LIKE ?', '%'. $filters['dish_name'] .'%');
		}
		
		if(isset($filters['status']))
		{
			$select->where('status = ?', $filters['status']);
		}
		
		if(isset($filters['dish_id']) && !empty($filters['dish_id']))
		{
			if(is_numeric($filters['dish_id']))
			{
				$select->where($table .'.id = ?', $filters['dish_id']);
			}
			elseif(is_array($filters['dish_id']))
			{
				$select->where($table .'.id IN (?)', $filters['dish_id']);
			}
		}
		
        $total = $storage->fetchRow($select);
        
        $total = intval($total["total"]);
                         	
    	return $total;
    }
    
    /**
     * Insert dish - ingredient
     * @param array $data
     */
    public static function insertDishIngredient($data)
    {
    	try
    	{
    		//Init data
    		$data = self::initDishIngredient($data);
    
    		if(empty($data))
    		{
    			return false;
    		}
    
    		//Get db instance
    		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    		 
    		$table = self::_TABLE_DISH_INGREDIENT;
    		 
    		$rs = $storage->insert($table, $data);
    		 
    		return $rs;
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('Dish::insertDishIngredient - '. $ex->getMessage());
    			
    		return false;
    	}
    }
    
    /**
     *
     * Delete an dish ingredient
     * @param int $dishId
     */
    public static function deleteDishIngredient($dishId)
    {
    	try
    	{
    		$dishId = intval($dishId);
    
    		if(empty($dishId))
    		{
    			return false;
    		}
    
    		// get storage instance
    		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    
    		$table = self::_TABLE_DISH_INGREDIENT;
    
    		// execute
    		$rs = $storage->delete($table, 'dish_id ='. $dishId);
    
    		return $rs;
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('Dish::deleteDishIngredient - '. $ex->getMessage());
    		return false;
    	}
    }
    
    /**
     * get detail of dish by alias
     * @param int $categoryAlias
     */
    public static function selectDishByAlias($alias)
    {
    	if(empty($alias))
    	{
    		return false;
    	}
    
    	if(isset(self::$_dish[$alias]))
    	{
    		return self::$_dish[$alias];
    	}
    
    	$dish = array();
    
    	try
    	{
    		//Get db instance
    		$storage = My_Zend_Globals::getStorage();
    
    		//Query data from database
    		$select = $storage->select()
			    		->from(self::_TABLE, '*')
			    		->where('alias = ?', $alias)
			    		->limit(1, 0);
    		
    		$dish = $storage->fetchRow($select);
    
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('Dish::selectDishByAlias - '. $ex->getMessage());    
    		return $dish;
    	}
    	
    	self::$_dish[$categoryAlias] = $dish;
    
    	return $dish;
    }
    
    /**
     *
     * get multi dish
     * @param array $arrayDishIds
     */
    public static function getMultiDish($arrayDishIds, $type = 0)
    {
    	try
    	{
    		$arrayDishIds = array_unique($arrayDishIds);
    		
    		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    
    		$table = self::_TABLE;
    		//Query data from database
    		$select = $storage->select()
			    		->from($table, '*')
			    		->where('status = ?', 1)
			    		->where('id IN (?)', $arrayDishIds)
			    		->limit($limit, $offset);
    
    		if(!empty($type))
    		{
    			$select->where('type = ?', $type);
    		}
    		
    		// query db
    		$list = $storage->fetchAll($select);
    
    		return  $list;
    	}
    	catch(Exception $e)
    	{
    		My_Zend_Logger::log('Dish::getMultiDish -- '.$e->getMessage());
    		return false;
    	}
    }
    
    /**
     *
     * get list ingredient in dish
     * @param int $dishId
     */
    public static function getIngredientInDish($dishId)
    {
    	$dataReturn = array();
    	try
    	{
    		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    
    		$table = self::_TABLE_DISH_INGREDIENT;
    		//Query data from database
    		$select = $storage->select()
			    		->from($table, 'ingredient_id')
			    		->where('dish_id = ?', $dishId);
    		
    		// query db
    		$list = $storage->fetchAll($select);
   			
    		if(!empty($list))
    		{
    			$arrayIngredientIds = array();
    			foreach($list as $item)
    			{
    				$arrayIngredientIds[] = $item['ingredient_id'];
    			}
    			
    			$dataReturn = Ingredient::getMultiIngredient($arrayIngredientIds);
    		}
    		
    		return  $dataReturn;
    	}
    	catch(Exception $e)
    	{	
    		My_Zend_Logger::log('Dish::getIngredientInDish -- '.$e->getMessage());
    		return false;
    	}
    }
    
    public static function dishUrl($dish, $absolute = false)
    {
    	if(empty($dish) || !is_array($dish))
    	{
    		return '';
    	}
    
    	$url = '/thuc-don/'.$dish['alias'].'.html';
    
    	if($absolute)
    		$url = BASE_URL . $url;
    
    	return $url;
    }
    
    /**
     * Init user taste
     * @param array $data
     */
    public static function initUserTaste($data)
    {
    	$fields = array('user_id', 'taste_id');
    
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
     * insert user taste
     * @param array $data
     */
    public static function insertUserTaste($data)
    {
    	try
    	{
    		//Init data
    		$data = self::initUserTaste($data);
    
    		if(empty($data))
    		{
    			return false;
    		}
    		
    		//Get db instance
    		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    		 
    		$table = self::_TABLE_USER_TASTE;
    		 
    		$rs = $storage->insert($table, $data);
    		 
    		return $rs;
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('UserTaste::insertUserTaste -- '. $ex->getMessage());   			
    		return false;
    	}
    }
    
    /**
     *
     * delete user taste
     * @param array $data
     */
    public static function deleteUserTaste($data)
    {
    	try
    	{
    		if(empty($data))
    		{
    			return false;
    		}
    
    		// get storage instance
    		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    
    		$table = self::_TABLE_USER_TASTE;
    
    		// execute
    		$rs = $storage->delete($table, 'taste_id ='. $data['taste_id'].' AND user_id = '.$data['user_id']);
    		
    		return $rs;
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('UserTaste::deleteUserTaste -- '. $ex->getMessage());
    		return false;
    	}
    }
    
    /**
     *
     * select list taste of user
     * @param int $setId
     */
    public static function getListTasteOfUser($userId)
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
	    	 
	    	$table = self::_TABLE_USER_TASTE;
	    	 
	    	$select = $storage->select()
	    				->from($table,array('taste_id'))
	    				->where('user_id = ? ', $userId);
	    	
	    	$data = $storage->fetchCol($select);
	    	 
	    	return $data;
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('UserTaste::getListTasteOfUser -- '. $ex->getMessage());
    		return false;
    	}
    }
}