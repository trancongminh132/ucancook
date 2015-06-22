<?php

class Admin 
{  
    public static $admin = null;	
      
    const _TABLE_ADMIN = 'admin';
    
    /**
     * Init data of user
     * @param array $data
     */
    public static function initAdmin($data) {       
        $fields = array('user_id', 'fullname', 'is_locked', 'updated_date', 'last_login_date', 'created_date');
        
        foreach ($fields as $field) 
        {
            if (isset($data[$field])) {
                $tmp[$field] = $data[$field];
            }
        }
        
        $rs = $tmp;
        return $rs;
    }
    
    public static function hashPassword($password, $salt)
    {
    	if (empty($password))
    	{
    		return FALSE;
    	}
    
    	return sha1($password . $salt);
    }
    
    public static function hashPasswordDb($userId, $password)
    {    	
    	if (empty($userId) || empty($password))
    	{
    		return false;
    	}
    	
    	//Get db instance
    	$storage = My_Zend_Globals::getStorage();
    	 
    	//Query
    	$select = $storage->select()
    						->from(User::_TABLE_USER, 'salt')
    						->where('user_id = ?', $userId)    				
    						->limit(1, 0);
    	    	    
    	$data = $storage->fetchRow($select);
    
    	if (count($data) !== 1)
    	{
    		return false;
    	}
        	
    	return self::hashPassword($password, $data['salt']);    	
    }
    
    public static function createAdminSalt($length = 10)
    {
    	$salt = '';
    	
    	for ($i = 0; $i < $length; $i++)
    	{
    		$salt .= chr(rand(33, 126));
    	}
    	
    	return $salt;
    }
    
    public static function login($email, $password)
    {
    	if (empty($email) || empty($password))
    	{
    		return false;
    	}
    	
    	try
    	{
	    	//Get db instance
	    	$storage = My_Zend_Globals::getStorage();
	    	
	    	//Query
	    	$select = $storage->select()
	    					->from(User::_TABLE_USER, '*')
	    					->where('email = ?', $email)
	    					->where('is_ban = 0')
	    					->limit(1, 0);
	    	
	    	$user = $storage->fetchRow($select);
	    	
	    	if (!empty($user))
	    	{
	    		$password = self::hashPasswordDb($user['user_id'], $password);
	    		
	    		$admin = Admin::getAdmin($user['user_id']);
	    		
	    		if ($user['password'] === $password && !empty($admin))
	    		{
	    			$auth = Zend_Auth::getInstance();
	    			$auth->setStorage(new Zend_Auth_Storage_Session('Adm'));
					$identity = new stdClass();
					$identity->user_id = $user['user_id'];
					$identity->email = $user['email'];
					$identity->role = $admin['role_id'];
					
					$auth->getStorage()->write($identity);
					
	    			return true;
	    		}
	    	}
	    
	    	return false;
    	}
    	catch(Exception $ex)
    	{
    		var_dump($ex->getMessage());die;
    		return false;
    	}
    }    
    
    /**
     * Insert user
     * @param array $data
     */
    public static function insertAdmin($data) {
        //Init data
        $data = self::initAdmin($data);

        if ($data === false) {
            return false;
        }

        //Get db instance
        $storage = My_Zend_Globals::getStorage();

        // Insert data
        $rs = $storage->insert(self::_TABLE_ADMIN, $data);
        
        // Return
        return $rs;
    }
  
    /**
     * Update user
     * @param array $data
     */
    public static function updateAdmin($data) 
    {
        $data = self::initAdmin($data);

        if ($data === false) {
            return false;
        }

        //Get db instance
        $storage = My_Zend_Globals::getStorage();
       
        //Update data
        $rs = $storage->update(self::_TABLE_ADMIN, $data, 'user_id=' . $data['user_id']);
       
        //Return
        return $rs;
    }

    /**
     * Select user
     * @param int $userId
     */
    public static function getAdmin($userId) {
    	
    	if (isset(self::$admin[$userId]))
            return self::$admin[$userId];
                 
    	//Get db instance
    	$storage = My_Zend_Globals::getStorage();

    	//Query
    	$select = $storage->select()
    					->from(self::_TABLE_ADMIN, '*')
    					->where('user_id = ?', $userId)
    					->limit(1, 0);
    	
    	$data = $storage->fetchRow($select);

    	//If empty
    	if (empty($data)) {
    		$data = array();
    	}
       	
        self::$admin[$userId] = $data;
        
        //Return
        return $data;
    }
    
    public static function getListAdmin($filters, $offset = 0,$limit = 30)
    {
    	$storage = My_Zend_Globals::getStorage();
    	 
    	$table = self::_TABLE_ADMIN;
    	 
    	//Query data from database
    	$select = $storage->select()
			    	->from($table,'*')
			    	->order('user_id ASC')
			    	->limit($limit, $offset);
    	 
    	if(isset($filters['is_locked']))
    	{
    		$select->where('is_locked = ?', $filters['is_locked']);
    	}
    	
    	$listAdmin = $storage->fetchAll($select);
    	
    	if(!empty($listAdmin))
    	{
	    	foreach ($listAdmin as &$user)
	    	{
	    		$userDetail = User::getUser($user['user_id']);
	    		$user['email'] = (isset($userDetail['email'])?$userDetail['email']:'');
	    	}
    	}
    	
    	return $listAdmin;
    }
    
    public static function getTotalListAdmin($filters)
    {
    	$storage = My_Zend_Globals::getStorage();
    	 
    	$table = self::_TABLE_ADMIN;
    	 
    	//Query data from database
    	$select = $storage->select()
    					->from($table,'count(user_id) as total');
    	 
    	if(isset($filters['is_locked']))
    	{
    		$select->where('is_locked = ?', $filters['is_locked']);
    	}
    	
    	$data = $storage->fetchRow($select);
    
    	return $data['total'];
    }
    
    public static function getListAdminByGroup($roleIds='all')
    {
    	try
    	{
    		//Get db instance
    		$storage = My_Zend_Globals::getStorage();
    
    		//Query
    		$select = $storage->select()
    		->from(self::_TABLE_ADMIN, '*')
    		->order('role_id ASC');
    
    		if($roleIds != 'all')
    		{
    			if(is_array($roleIds))
    			{
    				$select->where('role_id IN (?)', $roleIds);
    			}    		
    			else
    			{
    				$select->where('role_id = ?', $roleIds);
    			}
    		}
    
    		$select->where('is_locked = 0');
    
    		$data = $storage->fetchAll($select);
    
    		//If empty
    		if (empty($data)) {
    			$data = array();
    		}
    
    		foreach ($data as &$user)
    		{
    			$userDetail = self::selectAdmin($user['user_id']);
    			$user['user_name'] = (isset($userDetail['user_name'])?$userDetail['user_name']:'');
    		}
    
    		return $data;
    	}
    	catch(Exception $ex){    		    		
    		return false;
    	}
    }
}