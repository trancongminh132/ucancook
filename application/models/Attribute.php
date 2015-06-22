<?php
class Attribute
{    	
	// const tables
	const _TABLE = 'attribute';
	const _TABLE_ATTRIBUTE_OPTION = 'attribute_option';
	const _TABLE_ATTRIBUTE_CATEGORY = 'attribute_category';
	const _DB_INSTANCE = 'mysql';
    const TYPE_DROPDOWN = 'dropdown';    
    const TYPE_TEXT = 'text';    
    const TYPE_INTEGER	= 'int';    
    const TYPE_MULTIPLE = 'multiple';
    
    const ATTR_SPICE = 5;
    const ATTR_TOOL  = 7;
    const ATTR_SPEC = 6;
    
    public static $_INPUT_TYPE = array(
		self::TYPE_DROPDOWN,
		self::TYPE_TEXT,
		self::TYPE_INTEGER,
		self::TYPE_MULTIPLE
	);
    
    /**
     * Init data of attribute
     * @param array $data
     */
    public static function init($data)
    {        
        $fields = array('attribute_id','category_id', 'attribute_name', 'is_visible', 'is_require','input_type', 'updated_date');

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
     * 
     * Init attribute option data
     * @param array $data
     */
    public static function initAttributeOption($data)
    {
    	if(!isset($data['attribute_id']))
    	{
    		return false;
    	}
    	
    	$fields = array('option_id', 'attribute_id', 'value', 'sort_order');
    	
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
     * Init data of attribute category
     * @param array $data
     */
    public static function initAttributeCategory($data)
    {
    	$fields = array( 'category_id', 'attribute_id');

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
     * Insert attribute
     * @param array $data
     */
    public static function insert($data)
    {
    	try
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
	        
	        $attributeId = $storage->lastInsertId();
	               
	        return $attributeId;
    	}	
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::insert - '. $ex->getMessage());			
			return false;
		}
    }
    
	/**
     * Insert attribute option
     * @param array $data
     */
    public static function insertAttributeOption($data)
    {
    	try 
    	{
        	//Init data
	        $data = self::initAttributeOption($data);
	
	        if(empty($data))
	        {
	            return false;
	        }
			
	        //Get db instance
	        $storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	        
	        $table = self::_TABLE_ATTRIBUTE_OPTION;
	
	        // Insert data
	        $rs = $storage->insert($table, $data);
	        
	        $optionId = 0;
	        
	        if($rs)
	        {
	        	$optionId = $storage->lastInsertId();
	        }
	        
	        return $optionId;
    	}
    	catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::insertAttributeOption - '. $ex->getMessage());	
			return false;
		}
    }
    
	/**
     * Insert attribute category
     * @param array $data
     */
    public static function insertAttributeCategory($data)
    {
    	try 
    	{
	        //Init data
	        $data = self::initAttributeCategory($data);
	
	        if(empty($data))
	        {
	            return false;
	        }
	
	        //Get db instance
	        $storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	        
	        $table = self::_TABLE_ATTRIBUTE_CATEGORY;
		       
	        $rs = $storage->insert($table, $data);
	        
	        return $rs;
    	}
    	catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::insertAttributeCategory - '. $ex->getMessage());
			
			return false;
		}
    }
	
    /**
     * Update data
     * @param array $data
     */
    public static function update($data,$status='all')
    {
    	try
    	{
	        $data = self::init($data);
	
	        if(empty($data) || !isset($data['attribute_id']))
	        {
	            return false;
	        }
	
	        //Get db instance
	        $storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	
	        //Get partition
	        $table = self::_TABLE;
	
	      	$rs = $storage->update($table, $data, 'attribute_id='. $data['attribute_id']);
	      	
	        //Return
	        return $rs;
    	}
    	catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::update - '. $ex->getMessage());
			
			return false;
		}
    }
	
	/**
     * Update attribute option
     * @param array $data
     */
    public static function updateAttributeOption($data)
    {
    	try
    	{
	        $data = self::initAttributeOption($data);
	
	        if(empty($data))
	        {
	            return false;
	        }
	
	        //Get db instance
	        $storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	
	        //Get partition
	        $table = self::_TABLE_ATTRIBUTE_OPTION;
	        
	      	$rs = $storage->update($table, $data, 'option_id='. $data['option_id']);
	      	
	      	return $rs;
    	}
    	catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::updateAttributeOption - '. $ex->getMessage());			
			return false;
		}
    }
    	
    /**
     * 
     * Delete an attribute
     * @param int $attributeId
     */
    public static function delete($attributeId)
    {
    	try 
    	{
	    	$attributeId = intval($attributeId);
	    	
	    	if(empty($attributeId))
	    	{
	    		return false;
	    	}
	    	
	    	// get storage instance
	    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	    	
	    	$table = self::_TABLE;
	    	
	    	// execute
	    	$rs = $storage->delete($table, 'attribute_id ='. $attributeId );    	
	    	
	    	return $rs;	    	
    	}
    	catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::delete - '. $ex->getMessage());			
			return false;
		}
    }
    
	/**
     * 
     * Delete an attribute option by attribute id
     * @param int $attributeId
     */
    public static function deleteAttributeOptionByAttributeId($attributeId)
    {
    	try 
    	{
    			
	    	$attributeId = intval($attributeId);
	    	
	    	if(empty($attributeId))
	    	{
	    		return false;
	    	}
	    	
	    	// get storage instance
	    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	    	
	    	$table = self::_TABLE_ATTRIBUTE_OPTION;
	    	
	    	// execute
	    	$rs = $storage->delete($table, 'attribute_id ='. $attributeId );
	    	
	    	return $rs;
    	}
    	catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::deleteAttributeOptionByAttributeId - '. $ex->getMessage());			
			return false;
		}
    }
    
	/**
     * 
     * Delete an attribute option
     * @param int $optionId
     */
    public static function deleteAttributeOption($optionId)
    {
    	try 
    	{
	    	$optionId = intval($optionId);
	    	
	    	if(empty($optionId))
	    	{
	    		return false;
	    	}
	    	
	    	// get storage instance
	    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	    	
	    	$table = self::_TABLE_ATTRIBUTE_OPTION;
	    	
	    	// execute
	    	$rs = $storage->delete($table, 'option_id ='. $optionId );
	    	    	    	
	    	return $rs;
    	}
    	catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::deleteAttributeOption - '. $ex->getMessage());
			
			return false;
		}
    }
    
	/**
     * 
     * Delete an attribute category
     * @param int $setId
     */
    public static function deleteAttributeCategory($attributeId)
    {
    	try 
    	{
	    	$attributeId = intval($attributeId);
	    	
	    	if(empty($attributeId))
	    	{
	    		return false;
	    	}
	    	
	    	// get storage instance
	    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
	    	
	    	$table = self::_TABLE_ATTRIBUTE_CATEGORY;
	    	
	    	// execute
	    	$rs = $storage->delete($table, 'attributeId ='. $attributeId);
	    	
	    	return $rs;
    	}
    	catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::deleteAttributeCategory - '. $ex->getMessage());	
			return false;
		}
    }
    
    /**
     * 
     * Get attribute info
     * @param int $attributeId
     */
    public static function getAttribute($attributeId)
    {
    	try 
    	{
	    	$attributeId = intval($attributeId);
	    	
	    	if(empty($attributeId))
	    	{
	    		return false;
	    	}
	
	    	// get storage instance
	    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
			$table = self::_TABLE;
	    	$select = $storage->select()
	                    ->from(self::_TABLE, '*')
	                    ->where('attribute_id = ?', $attributeId);
	
	        $attribute = $storage->fetchRow($select);        
	                    
	        if(empty($attribute))
	        {
	            $attribute = array();
	        }
	        else 
	        {
	            if($attribute['input_type'] == self::TYPE_DROPDOWN || $attribute['input_type'] == self::TYPE_MULTIPLE)
	            {
	            	$attribute['options'] = self::getAttributeOption($attribute['attribute_id']);	          
	            }
	        }
	         
	    	return $attribute;
    	}
    	catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::getAttribute - '. $ex->getMessage());			
			return false;
		}
    }
    
	/**
     * 
     * Get list options of an attribute
     * @param int $attributeId
     */
    public static function getAttributeOption($attributeId)
    {
    	try 
    	{
	    	$attributeId = intval($attributeId);
	    	
	    	if(empty($attributeId))
	    	{
	    		return false;
	    	}
	    	// get storage instance
	    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
			$table = self::_TABLE_ATTRIBUTE_OPTION;
	    	$select = $storage->select()
	                    ->from($table, '*')
	                    ->where('attribute_id = ?', $attributeId)
	                    ->order(array('sort_order asc', ' value asc'));
	                
	        $options = $storage->fetchAll($select);          
	    	
	    	return $options;
    	}
    	catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::getAttributeOption - '. $ex->getMessage());
			
			return false;
		}
    }
    
  	/**
     * 
     * Get list attribute not in group
     * @param int $setId
     */
    public static function getListAttributeInCategory($categoryId)
    {
    	$categoryId = intval($categoryId);
    	
    	if(empty($categoryId))
    	{
    		return false;
    	}
		// get storage instance
    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    	
    	$table = self::_TABLE;
    	
    	$select = $storage->select()
             			  ->from($table,array('attribute_id'))            	
             			  ->where('category_id = ?',$categoryId);
		
        $listAttribute = $storage->fetchAll($select);         
                    	
    	return $listAttribute;
    }
    
  	
 	/**
     * 
     * Get List attribute
     * @param array $filters
     */
   	public static function getList($params = array(), $offset = 0, $limit = 30)
   	{
   		$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
   			
   		$table = self::_TABLE;
   			
   		//Query data from database
    	$select = $storage->select()
	    					->from($table,'*')
	    					->order('attribute_name asc')
	    					->limit($limit,$offset);
			
	    if($params['status'] != 'all')
	    {
	    	$select->where('is_visible = ?', $params['status']);
	    }
	    	
	    if(!empty($params['category_id']))
	    {
	    	$select->where('category_id = ?', $categoryId);
	    }

	    if(!empty($params['attribute_name']))
	    {
	    	$select->where('attribute_name like '."'%".$params['attribute_name']."%'");
	    }
	    // query db
	    $list = $storage->fetchAll($select);
   	
   		return $list;
   	}
   	
   	public function getTotal($params = array())
   	{
   		try
   		{   			   
		    // get storage instance
		    $storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
		    		
			$table = self::_TABLE;
					
		    $select = $storage->select()
		                    ->from($table, 'count(attribute_id) as total');          
	   		if($params['status'] != 'all')
		    {
		    	$select->where('is_visible = ?', $params['status']);
		    }
		    	
		    if(!empty($params['category_id']))
		    {
		    	$select->where('category_id = ?', $categoryId);
		    }
	
		    if(!empty($params['attribute_name']))
		    {
		    	$select->where('attribute_name like '."'%".$params['attribute_name']."%'");
		    }
		    
		    $total = $storage->fetchRow($select);
		                                   	
	    	return $total['total'];
   		}
   		catch(Exception $ex)
		{
			My_Zend_Logger::log('Attribute::getTotal - '. $ex->getMessage());		
			return false;
		}
   	}
   	
   	public function generateAttributeForm($attribute)
   	{
   		if(empty($attribute) || !is_array($attribute))
   		{
   			return '';
   		}
   		
   		$html = '';
   		
   		switch ($attribute['input_type'])
   		{
   			case 'checkbox':
   				$html .= '<input type="checkbox" value="1" name="'. $attribute['input_type'] .'_'. $attribute['attribute_id'] .'" id="'. $attribute['input_type'] .'_'. $attribute['attribute_id'] .'" /><label for="check5">Trắng</label>';
   				break;
   			
   			case 'text':
   				break;
   		}
   	}
   	
	/**
     * 
     * Get getAttributeOptionByOptionId
     * @param int $attributeId
     */
    public static function getAttributeOptionByOptionId($optionId)
    {
    	$optionId = intval($optionId);
    	
    	if(empty($optionId))
    	{
    		return false;
    	}

    	// get storage instance
    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
		$table = self::_TABLE_ATTRIBUTE_OPTION;
    	$select = $storage->select()
                    ->from($table, '*')
                    ->where('option_id = ?', $optionId);
                
        $option = $storage->fetchRow($select);
           
        return $option;
    } 

	/**
     * 
     * Get list attribute in category
     * @param int $categoryId
     */
    public static function getListAttributeByCategory($categoryId)
    {
    	$categoryId = intval($categoryId);
    	
    	if(empty($categoryId))
    	{
    		return false;
    	}
    	
    	// get storage instance
    	$storage = My_Zend_Globals::getStorage(self::_DB_INSTANCE);
    		
    	//get list attribute id with category id
		$table = self::_TABLE_ATTRIBUTE_CATEGORY;
					
		$tableAttribute = self::_TABLE;
		
    	$select = $storage->select()
                    ->from(array('gs' => $table),array('attribute_id'))
                    ->joinInner(array('g' => $tableAttribute),'gs.attribute_id = g.attribute_id', '*')
                    ->where('gs.category_id = ? ', $categoryId)
                    ->order('gs.attribute_id ASC') ;
         	
        $listAttributeIds = $storage->fetchAll($select);                        
         
        if(!empty($listAttributeIds))
        {            		
			foreach ($listAttributeIds as &$attribute)
		    {
		    	if($attribute['input_type'] == 'multiple' || $attribute['input_type'] == 'dropdown')
		    	{
		    		$attribute['options'] = self::getAttributeOption($attribute['attribute_id']);
		    	}		        
		    }   			
            $listAttributeIds = array_values($listAttributeIds);            	            	            	            	
        }  
        
    	return $listAttributeIds;    	
    }
}