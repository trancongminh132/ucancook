<?php
class Ingredient
{    
    const prefix_caching_ingredient = 'ingredient.';
    const prefix_caching_ingredient_list = 'ingredient.lst.';
    const prefix_caching_total_ingredient = 'ingredient.total';

	// const tables
	const _TABLE = 'ingredient';
    const _DB_INSTANCE = 1;   
    const _CACHE_INSTANCE = 0;
    
    public static $_ARRAY_UNIT = array(
    		1 => array(
    			'name' => '100 gram',
    		),
    		2 => array(
    			'name' => '1 kilogram'
    		),
    		3 => array(
    			'name' => '100 ml'
    		),
    		4 => array(
    			'name' => '1 lít'
    		)    		
    );
    const TYPE_SELL = 1;
    const TYPE_IN_MENU = 2;
    
    public static $_ARRAY_TYPE = array(
    	1 => 'Để bán',
    	2 => 'Làm nguyên liệu món ăn'
    );
    
    /**
     * Init data
     * @param array $data
     */
    public static function init($data)
    {        
        $fields = array('id', 'name', 'price', 'type', 'alias', 'category_id', 'image', 'special_price', 'unit_price', 'quantity', 'description','summary', 'created_date', 'updated_date');
		
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
        
        $ingredientId = $storage->lastInsertId();
       
        return $ingredientId;
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
     * @param array $ingredientId
     */  
    public static function delete($ingredientId)
    {
    	$ingredientId = intval($ingredientId);
  
    	if(empty($ingredientId))
    	{
    		return false;
    	}
    	
    	// get storage instance
    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    	
    	$table = self::_TABLE;
    	
    	// execute
    	$rs = $storage->delete($table, 'id ='. $ingredientId );
    	
    	return $rs;
    }
    
	/**
     * getItem
     * @param array $ingredientId
     */
    public static function getItem($ingredientId)
    {
    	$ingredientId = intval($ingredientId);
    	
    	if(empty($ingredientId))
    	{
    		return false;
    	}

    	// get storage instance
    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
		$table = self::_TABLE;
    	$select = $storage->select()
                    ->from(self::_TABLE, '*')
                    ->where('id = ?', $ingredientId);
		
        $ingredient = $storage->fetchRow($select);        
                    
        if(empty($ingredient))
        {
            $ingredient = array();
        }
         
    	return $ingredient;
    }
	
   	/**
     * 
     * get List
     * 
     */
	public static function getListItem($filters, $offset, $limit)
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
	    	
	    	if(isset($filters['ingredient_name']) && !empty($filters['ingredient_name']))
	    	{
	    		$select->where($table .'.name LIKE ?', '%'. $filters['ingredient_name'] .'%');
	    	}
	    	
	    	if(isset($filters['category_id']) && !empty($filters['category_id']))
	    	{
	    		$select->where('category_id = ?', $filters['category_id']);
	    	}
	    	
	    	if(isset($filters['sort']) && !empty($filters['sort']))
	    	{
	    		if($filters['sort'] == 'date_desc')
	    		{
	    			$select->order('updated_date DESC');
	    		}else{
	    			$select->order('special_price DESC');
	    		}
	    	}else{
	    		$select->order('updated_date DESC');
	    	}
	    	
	    	// query db
	    	$list = $storage->fetchAll($select);
			
	   		return  $list;
   		}
   		catch(Exception $e)
   		{
   			My_Zend_Logger::log('Ingredient::getListItem -- '.$e->getMessage());
			return false;
   		}   		
   	}
	
	/**
    * 
    * Get total 
	* 
    */
    public static function getTotalIngredient($filters)
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
    	
    	if(isset($filters['ingredient_name']) && !empty($filters['ingredient_name']))
    	{
    		$select->where($table .'.name LIKE ?', '%'. $filters['ingredient_name'] .'%');
    	}
    	
    	if(isset($filters['category_id']) && !empty($filters['category_id']))
    	{
    		$select->where('category_id = ?', $filters['category_id']);
    	}
    	
        $total = $storage->fetchRow($select);
        
        $total = intval($total["total"]);
                         	
    	return $total;
    }
    
    /**
     *
     * get multi ingredient
     * @param array $arrayIngredientIds
     */
    public static function getMultiIngredient($arrayIngredientIds)
    {
    	try
    	{
    		$arrayIngredientIds = array_unique($arrayIngredientIds);
    
    		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    
    		$table = self::_TABLE;
    		//Query data from database
    		$select = $storage->select()
			    		->from($table, '*')
			    		->where('id IN (?)', $arrayIngredientIds)
			    		->limit($limit, $offset);
    		
    		// query db
    		$list = $storage->fetchAll($select);
    
    		return  $list;
    	}
    	catch(Exception $e)
    	{
    		My_Zend_Logger::log('Dish::getMultiIngredient -- '.$e->getMessage());
    		return false;
    	}
    }
    
    public static function ingredientUrl($data, $absolute = false)
    {
    	if(empty($data) || !is_array($data))
    	{
    		return '';
    	}
    
    	$url = '/nguyen-lieu/'.$data['alias'].'.html';
    
    	if($absolute)
    		$url = BASE_URL . $url;
    
    	return $url;
    }
    
    /**
     * get detail of ingredient by alias
     * @param int $alias
     */
    public static function getItemByAlias($alias)
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
    
    		//Query data from database
    		$select = $storage->select()
			    		->from(self::_TABLE, '*')
			    		->where('alias = ?', $alias)
			    		->limit(1, 0);
    
    		$data = $storage->fetchRow($select);
    
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('Ingredient::getItemByAlias - '. $ex->getMessage());
    		return $data;
    	}
    	
    	return $data;
    }
}