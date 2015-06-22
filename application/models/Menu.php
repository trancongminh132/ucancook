<?php
class Menu
{    
    const prefix_caching_menu = 'menu.';
    const prefix_caching_menu_list = 'menu.lst.';
    const prefix_caching_total_menu = 'menu.total';

	// const tables
	const _TABLE = 'menu';
    const _DB_INSTANCE = 1;   
    const _CACHE_INSTANCE = 0;
    
    /**
     * Init data
     * @param array $data
     */
    public static function init($data)
    {        
        $fields = array('id', 'dish_id', 'quantity', 'sale_date', 'created_date');
		
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
        $storage = My_Zend_Globals::getStorage();
      
        $table = self::_TABLE;
		
        // Insert data
        $rs = $storage->insert($table, $data);
        
        $menuId = $storage->lastInsertId();
       
        return $menuId;
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
        $storage = My_Zend_Globals::getStorage();

        //Get partition
        $table = self::_TABLE;

      	$rs = $storage->update($table, $data, 'id='. $data['id']);
      
        //Return
        return $rs;
    }
	
	/**
     * delete
     * @param array $menuId
     */  
    public static function delete($menuId)
    {
    	$menuId = intval($menuId);
  
    	if(empty($menuId))
    	{
    		return false;
    	}
    	
    	// get storage instance
    	$storage = My_Zend_Globals::getStorage();
    	
    	$table = self::_TABLE;
    	
    	// execute
    	$rs = $storage->delete($table, 'id ='. $menuId );
    	
    	return $rs;
    }
    
	/**
     * getItem
     * @param array $menuId
     */
    public static function getItem($menuId)
    {
    	$menuId = intval($menuId);
    	
    	if(empty($menuId))
    	{
    		return false;
    	}

    	// get storage instance
    	$storage = My_Zend_Globals::getStorage();
		$table = self::_TABLE;
    	$select = $storage->select()
                    ->from(self::_TABLE, '*')
                    ->where('id = ?', $menuId);
		
        $menu = $storage->fetchRow($select);        
                    
        if(empty($menu))
        {
            $menu = array();
        }
         
    	return $menu;
    }
	
   	/**
     * 
     *  get List
     *  @param $filters
     */
	public static function getListMenu($filters, $offset, $limit)
   	{
   		try
   		{		
	   		$storage = My_Zend_Globals::getStorage();
	   		
	   		$table = self::_TABLE;
	   		//Query data from database
	    	$select = $storage->select()
	    				  ->from($table, '*')
	    				  ->limit($limit, $offset);
	    	
			if(isset($filters['dish_id']) && !empty($filters['dish_id']))
			{
				$select->where('dish_id = ?', $filters['dish_id']);
			}
			
			if(isset($filters['sale_date']) && !empty($filters['sale_date']))
			{
				$beginDate = ProductOrders::beginDate($filters['sale_date']);
				$endDate = ProductOrders::endDate($filters['sale_date']);
				$select->where('sale_date <= ?', $endDate);
				$select->where('sale_date >= ?', $beginDate);
			}
			
			// query db
			$list = $storage->fetchAll($select);
			
	   		return  $list;
   		}
   		catch(Exception $e)
   		{
   			My_Zend_Logger::log('Menu::getListItem -- '.$e->getMessage());
			return false;
   		}   		
   	}
	
	/**
     * 
     *  getListItemByDay
     *  @param $date
     */
	public static function getListItemByDay($date, $type = 0, $offset, $limit)
   	{
   		try
   		{	
			$dataReturn = array();
   			
			$beginDate = ProductOrders::beginDate($date);
			$endDate = ProductOrders::endDate($date);
		
	   		$storage = My_Zend_Globals::getStorage();
	   		
	   		$table = self::_TABLE;
	   		//Query data from database
	    	$select = $storage->select()
	    				  ->from($table, '*')
						  ->where('sale_date >= ?', $beginDate)
						  ->where('sale_date <= ?', $endDate)
	    				  ->limit($limit, $offset);
	    	
	    	//query db
	    	$list = $storage->fetchAll($select);
			
	    	if(!empty($list))
	    	{
	    		$arrayDishId = array();
	    		foreach($list as $item)
	    		{
	    			$arrayDishIds[] = $item['dish_id'];
	    		}
	    		
	    		$dataReturn = Dish::getMultiDish($arrayDishIds, $type);
	    	}
	    	
	   		return  $dataReturn;
   		}
   		catch(Exception $e)
   		{
   			My_Zend_Logger::log('Menu::getListItemByDay -- '.$e->getMessage());
			return false;
   		}   		
   	}
	
	/**
     * 
     *  get List Item In Week Of Dish
     *  @param $dishId
	 *  @param $date
     */
	public static function getListItemInWeekOfDish($dishId, $date = NOW, $offset = 0, $limit = 4)
   	{
   		try
   		{		
			$date_string = date('Y', $date) . 'W' . sprintf('%02d', date('W', $date));
			$startWeek = strtotime($date_string);
			$startWeek = ProductOrders::beginDate($startWeek);
			
			$endWeek = strtotime($date_string . '7');
			$endWeek = ProductOrders::endDate($endWeek);
			
	   		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	   		
	   		$table = self::_TABLE;
	   		//Query data from database
	    	$select = $storage->select()
	    				  ->from($table, array('sale_date'))
	    				  ->where('dish_id = ?', $dishId)
						  ->where('sale_date >= ?', $startWeek)
						  ->where('sale_date <= ?', $endWeek)
						  ->order('sale_date ASC')
	    				  ->limit($limit, $offset);
	    	
	    	//query db
	    	$list = $storage->fetchAll($select);
			
	   		return  $list;
   		}
   		catch(Exception $e)
   		{
   			My_Zend_Logger::log('Menu::getListItemInWeekOfDish -- '.$e->getMessage());
			return false;
   		}   		
   	}
	
	/**
     * 
     *  get List Item next Week Of Dish
     *  @param $dishId
	 *  @param $date
     */
	public static function getListItemNextWeekOfDish($dishId, $numWeek = 1, $date = NOW, $offset = 0, $limit = 4)
   	{
   		try
   		{		
			$week = date('W', $date)+$numWeek;
			$date_string = date('Y', $date) . 'W' . sprintf('%02d', $week);
			$startWeek = strtotime($date_string);
			$startWeek = ProductOrders::beginDate($startWeek);
			
			$endWeek = strtotime($date_string . '7');
			$endWeek = ProductOrders::endDate($endWeek);
			
	   		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	   		
	   		$table = self::_TABLE;
	   		//Query data from database
	    	$select = $storage->select()
	    				  ->from($table, array('sale_date'))
	    				  ->where('dish_id = ?', $dishId)
						  ->where('sale_date >= ?', $startWeek)
						  ->where('sale_date <= ?', $endWeek)
						  ->order('sale_date ASC')
	    				  ->limit($limit, $offset);
	    	
	    	//query db
	    	$list = $storage->fetchAll($select);
			
	   		return  $list;
   		}
   		catch(Exception $e)
   		{
   			My_Zend_Logger::log('Menu::getListItemNextWeekOfDish -- '.$e->getMessage());
			return false;
   		}   		
   	}
	
	/**
    * 	
    * 	get total
	* 	@param $filters
    */	
    public static function getTotalMenu()
    {      	
    	// get storage instance
    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    		
		$table = self::_TABLE;
			
    	$select = $storage->select()
						->from($table, 'count(id) as total');
        
		if(isset($filters['dish_id']) && !empty($filters['dish_id']))
		{
			$select->where('dish_id = ?', $filters['dish_id']);
		}
		
		if(isset($filters['sale_date']) && !empty($filters['sale_date']))
		{
			$beginDate = ProductOrders::beginDate($filters['sale_date']);
			$endDate = ProductOrders::endDate($filters['sale_date']);
			$select->where('sale_date <= ?', $endDate);
			$select->where('sale_date >= ?', $beginDate);
		}
		
        $total = $storage->fetchRow($select);
        
        $total = intval($total["total"]);
                         	
    	return $total;
    }
}