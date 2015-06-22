<?php
class User 
{
    public static $user = null;

    const _TABLE_USER = 'users';
    const _SOCIAL_TYPE_FACEBOOK = 'facebook';
    const _SOCIAL_TYPE_GOOGLE = 'google';

    /**
     * Init data of user
     * @param array $data
     */
    public static function initUser($data) {
        $fields = array('user_id', 'user_name', 'display_name', 'birthday', 'email', 'password', 'salt', 'gender', 'address','city_id', 'district_id', 'mobile', 'is_ban', 'registered_date', 'last_update', 'banned_date', 'receive_email', 'social_id', 'social_type');
		
        foreach ($fields as $field)
        {
            if (isset($data[$field])) {
                $tmp[$field] = $data[$field];
            }
        }

        $rs = $tmp;
        return $rs;
    }

    /**
     * Insert user
     * @param array $data
     */
    public static function insertUser($data) 
    {
        //Init data
        $data = self::initUser($data);

        if ($data === false) {
            return false;
        }

        try {
	        //Get db instance
	        $storage = My_Zend_Globals::getStorage();

	        // Insert data
	        $rs = $storage->insert(self::_TABLE_USER, $data);

	        if($rs)
	        {
	        	$rs = $storage->lastInsertId();
	        }

	        // Return
	        return $rs;
        }
        catch(Exception $ex)
        {
        	return false;
        }
    }

    /**
     * Update user
     * @param array $data
     */
    public static function updateUser($data) {
        $data = self::initUser($data);

        if ($data === false) {
            return false;
        }

        //Get db instance
        $storage = My_Zend_Globals::getStorage();

        //Update data
        $rs = $storage->update(self::_TABLE_USER, $data, 'user_id=' . $data['user_id']);

        //Return
        return $rs;
    }

    /**
     * Select user
     * @param int $userId
     */
    public static function getUser($userId) 
    {
    	//Get db instance
    	$storage = My_Zend_Globals::getStorage();

    	//Query
    	$select = $storage->select()
    					->from(self::_TABLE_USER, '*')
    					->where('user_id = ?', $userId)
    					->limit(1, 0);

    	$data = $storage->fetchRow($select);

    	//If empty
    	if (empty($data)) {
    		$data = array();
    	}
		
        //Return
        return $data;
    }
	
    public static function getUserByEmail($email)
    {
    	try
    	{
	    	//Get db instance
	    	$storage = My_Zend_Globals::getStorage();

	    	//Query
	    	$select = $storage->select()
	    			->from(self::_TABLE_USER, 'email')
	    			->where('email = ?', $email)
	    			->where('social_type IS NOT NULL')
	    			->limit(1, 0);

	    	$data = $storage->fetchRow($select);

	    	//If empty
	    	if (empty($data)) {
	    		$data = array();
	    	}

	    	//Return
	    	return $data;
    	}
    	catch(Exception $ex)
    	{
    		return array();
    	}
    }

    public static function getUserBySocialId($socialId, $socialType)
    {
    	try
    	{
    		//Get db instance
    		$storage = My_Zend_Globals::getStorage();

    		//Query
    		$select = $storage->select()
    		->from(self::_TABLE_USER, '*')
    		->where('social_id = ?', $socialId)
    		->where('social_type = ?', $socialType)
    		->limit(1, 0);

    		$data = $storage->fetchRow($select);

    		//If empty
    		if (empty($data)) {
    			$data = array();
    		}

    		//Return
    		return $data;
    	}
    	catch(Exception $ex)
    	{
    		return array();
    	}
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
    				->from(self::_TABLE_USER, 'salt')
    				->where('user_id = ?', $userId)
    				->limit(1, 0);

    	$data = $storage->fetchRow($select);

    	if (count($data) !== 1)
    	{
    		return false;
    	}

    	return self::hashPassword($password, $data['salt']);
    }

    public static function createUserSalt($length = 10)
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
    					->from(self::_TABLE_USER, '*')
    					->where('email = ?', $email)
    					->where('is_ban = 0')
    					->limit(1, 0);

    		$user = $storage->fetchRow($select);

    		if (!empty($user))
    		{
    			$password = self::hashPasswordDb($user['user_id'], $password);
				
    			if ($user['password'] === $password)
    			{
    				return self::setlogin($user['user_id'], $password);
    			}
    		}

    		return FALSE;
    	}
    	catch(Exception $ex)
    	{
    		return false;
    	}
    }

    public static function setlogin($userId, $email)
    {
    	$auth = Zend_Auth::getInstance();
    	$auth->setStorage(new Zend_Auth_Storage_Session('Default'));
    	$identity = new stdClass();
    	$identity->user_id = $userId;
    	$identity->email = $email;

    	$auth->getStorage()->write($identity);
    	return true;
    }
    
    public static function getUserId($userName)
    {
    	$userName = trim($userName);
    	 
    	if(empty($userName))
    	{
    		return 0;
    	}
    	 
    	try
    	{  		
    		//Get db instance
    		$storage = My_Zend_Globals::getStorage();
    
    		//Query data from database
    		$tableUser = 'users';
    
    		$select = $storage->select()
		    			->from($tableUser, 'user_id')
		    			->where('user_name = ?', $userName);
    
    		$userId = $storage->fetchRow($select);
  			
    		//If empty
    		if(empty($userId))
    		{
    			$userId = 0;
    		}
    		else
    		{
    			$userId = $userId['user_id'];
    		}
       		
    		return $userId;
    	}
    	catch(Exception $ex)
    	{
    		return 0;
    	}
    }
    
    public static function getUserIdByEmail($email)
    {
    	$email = trim($email);
    
    	if(empty($email))
    	{
    		return 0;
    	}
    
    	try
    	{
    		//Get db instance
    		$storage = My_Zend_Globals::getStorage();
    
    		//Query data from database
    		$tableUser = 'users';
    
    		$select = $storage->select()
			    		->from($tableUser, 'user_id')
			    		->where('email = ?', $email);
    
    		$userId = $storage->fetchRow($select);
    			
    		//If empty
    		if(empty($userId))
    		{
    			$userId = 0;
    		}
    		else
    		{
    			$userId = $userId['user_id'];
    		}
    		 
    		return $userId;
    	}
    	catch(Exception $ex)
    	{
    		return 0;
    	}
    }
    
    public static function getListUser($filters, $offset = 0,$limit = 30)
    {
    	$storage = My_Zend_Globals::getStorage();
    
    	$table = self::_TABLE_USER;
    
    	//Query data from database
    	$select = $storage->select()
			    	->from($table,'*')
			    	->order('user_id ASC')
			    	->limit($limit, $offset);
    	
    	if(isset($filters['email']) && !empty($filters['email']))
    	{
    		$select->where('email = ?', $filters['email']);
    	}
    	
    	if(isset($filters['is_ban']))
    	{
    		$select->where('is_ban = ?', $filters['is_ban']);
    	}
    	
    	$list = $storage->fetchAll($select);
    	
    	return $list;
    }
    
    public static function getTotalUser($filters)
    {
    	$storage = My_Zend_Globals::getStorage();
    
    	$table = self::_TABLE_USER;
    
    	//Query data from database
    	$select = $storage->select()
    				->from($table,'count(user_id) as total');
    	
    	if(isset($filters['email']) && !empty($filters['email']))
    	{
    		$select->where('email = ?', $filters['email']);
    	}
    	 
    	if(isset($filters['is_ban']))
    	{
    		$select->where('is_ban = ?', $filters['is_ban']);
    	}
    	
    	$data = $storage->fetchRow($select);
    
    	return $data['total'];
    }
    
    public static function generateUserAddress($addStreetNumberWard, $addDistrictId, $addCityId)
    {
    
    	include_once APPLICATION_PATH .'/configs/cities.php';
    	global $_DISTRICTS, $_CITIES;
    
    	$address = $addStreetNumberWard;
    	
    	if($addDistrictId > 0 && isset($_DISTRICTS[$addCityId][$addDistrictId]))
    	{
    		$address .= ', '. $_DISTRICTS[$addCityId][$addDistrictId]['district_name'];
    	}
    
    	if($addCityId > 0 && isset($_CITIES[$addCityId]))
    	{
    		$address .= ', '.$_CITIES[$addCityId]['city_name'];
    	}
    		
    	return $address;
    }
    
    public static function insertFromSocialProfile($fbProfile, $userSocialId, $socialType)
    {
    	$userData = array (
    		'me_id' => $fbProfile['id'],
    		'display_name' => $fbProfile['name'],
    		'registered_date' => time(),
    		'last_update'	=> time(),
    		'is_verified_email' => 1,
    		'social_id' => $userSocialId,
    		'social_type' => $socialType
    	);
    	
    	if(isset($fbProfile['birthday']))
    	{
    		$birthday = $fbProfile['birthday'];
    		$date = new DateTime($birthday);
    		$date->format("m/d/Y");
    		$userData['birthday'] = $date->getTimestamp();
    	}
    	
    	if (isset($fbProfile['gender']) )
    	{
    		$userData['gender'] = $fbProfile['gender'] == 'male' ? 1 : 0;
    	}
    	
    	if(isset($fbProfile['email']))
    	{
    		$userData['email'] = $fbProfile['email'];
    	}
    
    	$startupResult = self::insertUser($userData);
    	
    	if($startupResult)
    		return $startupResult;
    	
    	return false;
    }
    
}