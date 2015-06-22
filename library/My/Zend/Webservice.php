<?php
class My_Zend_Webservice
{ 
	//Curl instance
	private static $curl = null;
	
    /**
     * Constructor
     *
     */
	private final function __construct() {}
	
    /**
     * Destructor
     *
     */
	public final function __destruct()
	{
		if(isset(self::$curl))
		{
			//Close curl
			curl_close(self::$curl);
		}
	    
		//Unset curl
		unset(self::$curl);
	}
	
    /**
	 * Clone function
	 *
	 */
	private final function __clone() {}
	
	/**
	 * Set instance
	 */
	public final static function setInstance()
	{
	    //Put instance
		if(!isset(self::$curl))
		{
			self::$curl = curl_init();
		}
	}
	
	/**
	 * Lay thong tin cua mot nhom nguoi choi game tu Service cua Me
	 * @param string $api_url
	 * @param string $list
	 * @param boolean $byUsername
	 * @return array
	 */
	public static function getListProfileInfoByMe($api_url, $list='',$byUsername='0')
	{
		//Set instance
		self::setInstance();
		
		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'list'        =>    $list,
		    'byusername'  =>    $byUsername,
		    'method'      =>    'getProfileInfo'
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
	    
		//Check response
		if(empty($response))
		{
			return array();
		}
		
		//Return data
		$response = trim($response);				
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * Lay thong tin cua mot nguoi choi game tu Service cua Me
	 * @param string $api_url
	 * @param string $list
	 * @param boolean $byUsername
	 * @return array
	 */
	public static function getProfileInfoByMe($api_url, $username='',$byUsername='1')
	{
		$array_me = self::getListProfileInfoByMe($api_url, $username, $byUsername) ;
		return (sizeof($array_me) > 0 ? $array_me[0] : array());
	}
	
    /**
	 * Lay thong tin full cua mot nhom nguoi choi game tu Service cua Me
	 * @param string $api_url
	 * @param string $list
	 * @param boolean $byUsername
	 * @return array
	 */
	public static function getListFullProfileInfoByMe($api_url, $list='',$byUsername='0')
	{
		//Set instance
		self::setInstance();
		
		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'list'        =>    $list,
		    'byusername'  =>    $byUsername,
		    'method'      =>    'getFullProfileInfo'
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
	    
		//Check response
		if(empty($response))
		{
			return array();
		}
		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * Lay thong tin full cua mot nguoi choi game tu Service cua Me
	 * @param string $api_url
	 * @param string $list
	 * @param boolean $byUsername
	 * @return array
	 */
	public static function getFullProfileInfoByMe($api_url, $username='',$byUsername='1')
	{
		$array_me = self::getListFullProfileInfoByMe($api_url, $username, $byUsername) ;
		return (sizeof($array_me) > 0 ? $array_me[0] : array());
	}
	
	/**
	 * Lay toan danh sach ban be chi tiet cua mot gamer theo username cua Me
	 * @param string $api_url
	 * @param string $username
	 * @return array
	 */
	public static function getFriendListByMe($api_url, $username)
	{
		//Set instance
		self::setInstance();
		
		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'username_from'  =>  $username,
		    'method'         =>  'getFriendList'
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
		
		//Check response
		if(empty($response))
		{
			return array();
		}
		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * Cap nhat thong tin
	 * @param string $api_url
	 * @param string $username
	 * @param int $gender
	 * @param string $dob
	 * @return boolean
	 */
	public static function updateProfileShortByMe($api_url, $username, $user_profile)
	{
		//Set instance
		self::setInstance();
		
		//Serialize
   		$user_profile = serialize($user_profile);
   		
   		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'username'      =>  $username,
		    'user_profile'  =>  $user_profile,
		    'method'        =>  'updateProfile'
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
   		
   		//Check response
		if(empty($response))
		{
			return 0;
		}
   		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data']['result'] : 0) ;	
	}
	
	/**
	 * Lay toan danh sach ban be theo meid cua mot gamer theo username cua Me
	 * @param string $api_url
	 * @param string $username
	 * @return array
	 */
	public static function getFriendListIdByUsername($api_url, $username)
	{
	    //Set instance
		self::setInstance();
		   		
   		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'username_from'  =>  $username,
		    'method'         =>  'getFriendListId'
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
   		
   		//Check response
		if(empty($response))
		{
			return 0;
		}
		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * Lay danh sach ID list cua ban be
	 * @param string $api_url
	 * @param int $list_user_id
	 * @return array
	 */
	public static function getFriendListIdByUserid($api_url, $userid)
	{
		//Set instance
		self::setInstance();
		   		
   		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'list_user_id'  =>  $userid,
		    'method'        =>  'getFriendListIdByUserid'
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
		
		//Check response
		if(empty($response))
		{
			return array();
		}
		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * Push Feed den trang chu $meid cua Me
	 * @param string $api_url 
	 * @param int $meid
	 * @param array $feedata
	 * @param int $actionId
	 * @param int $appid
	 * @param string $flag : @friend, @follow, @me-follow, @me-friend
	 * @return boolean
	 */
	public static function pushFeedMe($api_url,$feedata)
	{
		$appID = $feedata['appid'];
		$actionId = $feedata['actionid'];
		$userId = $feedata['userid'];
		$objectId = $feedata['objectid'];
		$isFeature = $feedata['isfeature'];		
		$arrData = $feedata['data'];
		
		
		//Add data
		if(empty($arrData['flag_feed']))
		{
			$arrData['flag_feed'] = '@all';	
		}			
		$arrData['version'] = 2 ;	

		//Json data
		$json = urlencode( base64_encode(Zend_Json::encode($arrData)) );

		//Call Api                 	
    	$url = $api_url . "?method=insertFeedByUserIdV2&appId=$appID&actionId=$actionId&userId=$userId&objectId=$objectId&isFeature=$isFeature&jsonData=$json" ;
		$response = file_get_contents($url); 
		
    	//Return data		
		return 1 ;    	
	}
	
	/**
	 * Goi email thong bao trong he thong
	 * @param string $api_url 
	 * @param int $actionid
	 * @param string $receiver
	 * @param array $data
	 * @param int $productid
	 * @param int $serviceid
	 * @param string $sender
	 * @return boolean
	 */
	public static function pushNotification($api_url,$actionid,$receiver,$data, $productid=22,$serviceid=1)
	{ 
		//Set data
		$notification = new My_Zend_Webservice_Data_Notification(); 
		$notification->productid     = $productid;
		$notification->actionid      = $actionid;
		$notification->serviceid     = $serviceid;
		$notification->sendername    = '123mua';		
		$notification->receiver[]    = $receiver;			
		$notification->jsondata      = Zend_Json::encode($data);	
		$notification->fromusername  = isset($data['fromusername'])?$data['fromusername']:'support.123mua';
		$notification->tousername    = isset($data['tousername'])?$data['tousername']:'support.123mua';
		
		//Debug data
		$url = $api_url . '?method=PushNotification&content='.urlencode(base64_encode( Zend_Json::encode($notification)));		
		/*echo "Notification :".$url."\n";
		echo "Data :\n";
		var_dump($notification); echo "\n";*/		
		$response =  file_get_contents($url);
		/*echo "Response :\n";
		var_dump($response); echo "\n";*/
				
		//Return data		
		return 1 ;		
	} 

	/**
	 * Notify qua docking bar
	 * @param string $api_url
	 * @param int $appid
	 * @param int $meid
	 * @param string $body
	 */
	public static function notifyDockingBar($api_url,$appid,$meid,$body)
	{	
		//Add data
		$body = urlencode($body);
						
		//Debug data	        	            	
    	$url = $api_url . "?method=addNotification&appid=$appid&userid=$meid&body=$body" ;    	
		//echo "DockingBar :".$url."\n";		
    	$response = file_get_contents($url); 
		/*echo "Response :\n";
		var_dump($response); echo "\n";*/
		
    	//Return data		
		return 1 ;  		
	}

	/**
	 * Get rating
	 * @param string $api_url
	 * @param int $productid
	 * @param int $itemid
	 * @param int $categoryid
	 * @param string $url
	 * @param string $responsetype
	 * @param string $callback
	 */
	public static function getRating($api_url,$itemid,$categoryid='',$url='',$productid=150,$responsetype='json',$callback='')
	{
		//Set instance
		self::setInstance();
		
		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'command'      =>  'rating',
		    'productid'    =>  $productid,		    
		    'itemid'       =>  $itemid,
		    'url'          =>  $url,
		    'categoryid'   =>  $categoryid,
		    'responsetype' =>  $responsetype,
		    'callback'     =>  $callback
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
		
		//Check response
		if(empty($response))
		{
			return array();
		}
		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * API thống kê tài sản của user theo privacy trên Zing Me Photo
	 * @param string $api_url
	 * @param string $username
	 * @param int $privacy
	 */
	public static function getStatsByPrivacy($api_url,$username,$privacy=1)
	{
		//Set instance
		self::setInstance();
		
		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'method'       =>  'getStatsByPrivacy',
		    'privacy'      =>  $privacy
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
		
		//Build url
		$api_url = $api_url . '/' . $username . '/api/rest';
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
		
		//Check response
		if(empty($response))
		{
			return array();
		}
		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * API lấy danh sách Album của một user theo privacy
	 * @param string $api_url
	 * @param string $username
	 * @param int $page
	 * @param int $limit
	 * @param int $privacy
	 */
	public static function getAlbumListByUserInfo($api_url,$username,$page=0,$limit=10,$privacy=1)
	{
		//Set instance
		self::setInstance();
		
		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'method'       =>  'getAlbumListByUserInfo',
		    'privacy'      =>  $privacy,
		    'page'         =>  $page,
		    'limit'        =>  $limit
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
		
		//Build url
		$api_url = $api_url . '/' . $username . '/api/rest';
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
		
		//Check response
		if(empty($response))
		{
			return array();
		}
		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * API lấy danh sách album theo thể loại
	 * @param unknown_type $api_url
	 * @param unknown_type $username
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $cateid
	 */
	public static function getAlbumListByCateId($api_url,$username,$cateid=0,$page=0,$limit=10)
	{
		//Set instance
		self::setInstance();
		
		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'method'       =>  'getAlbumListByCateId',
		    'cateid'      =>  $cateid,
		    'page'         =>  $page,
		    'limit'        =>  $limit
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
		
		//Build url
		$api_url = $api_url . '/' . $username . '/api/rest';
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
		
		//Check response
		if(empty($response))
		{
			return array();
		}
		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * API lấy danh sách photo trong một album theo album id
	 * @param string $api_url
	 * @param string $username
	 * @param int $albumID
	 * @param int $page
	 * @param int $limit
	 */
	public static function getPhotoListByAlbumID($api_url,$username,$albumID=0,$page=0,$limit=10)
	{
		//Set instance
		self::setInstance();
		
		/**
		 * Add params
		 * @var array
		 */
		$params = array(
		    'method'       =>  'getAlbumListByCateId',
		    'albumID'      =>  $albumID,
		    'page'         =>  $page,
		    'limit'        =>  $limit
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
		
		//Build url
		$api_url = $api_url . '/' . $username . '/api/rest';
				
		//Post curl
	    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
	    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
		curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	    curl_setopt(self::$curl, CURLOPT_POST, true);
	    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
	    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
		
	    //Get respone
		$response = curl_exec(self::$curl);	
		
		//Check response
		if(empty($response))
		{
			return array();
		}
		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * Add vote
	 * @param string $api_url
	 * @param int $product_id
	 * @param int $item_id
	 * @param int $owner_id
	 * @param string $user_vote
	 * @param int $vote_mark
	 */
	public static function addVoting($api_url, $product_id, $item_id, $owner_id, $user_vote, $vote_name, $vote_mark=1)
	{
		//Debug data	        	            	
    	$url = $api_url . "?method=addItemVote&product_id=$product_id&item_id=$item_id&owner_id=$owner_id&user_vote=$user_vote&vote_name=$vote_name&vote_mark=$vote_mark" ;    	
		/*echo "Vote url :".$url."\n";*/		
    	$response = file_get_contents($url); 
		/*echo "Response :\n";
		var_dump($response); echo "\n";*/
		
		//Check response
		if(empty($response))
		{   
			return array();
		}
		
		//Return data
		$response = trim($response);
		$response = Zend_Json::decode($response);	
		return (($response['error_code'] == 0) ? $response['data'] : array()) ;
	}
	
	/**
	 * 
	 * API lấy danh sách blog da publish theo username
	 * @param string $api_url
	 * @param string $username
	 * @param int $page
	 * @param int $limit
	 * @param int $privacy
	 */
	public static function getBlogListByUser($api_url,$username,$page=1,$limit=10,$privacy=0)
	{
		//Set instance
		self::setInstance();
		
		/**
		 * Add params
		 * @var array
		 */
                //http://api.blog.zing.vn/api/rest?method=getBlogListByUser&user_name=support.123mua&page=1&size=10&privacy=0&viewer_name=support.123mua
		
                $params = array(
		    'method'       =>  'getBlogListByUser',
		    'user_name'    =>  $username,
		    'page'         =>  $page,
		    'size'         =>  $limit,
		    'privacy'      =>  $privacy,
                    'viewer_name'  =>  $username
		);
		
		//Build params
		$params = http_build_query($params, null, '&');
		try {
					//Post curl
		    curl_setopt(self::$curl, CURLOPT_URL, $api_url);
		    curl_setopt(self::$curl, CURLOPT_HEADER, false);			
			curl_setopt(self::$curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		    curl_setopt(self::$curl, CURLOPT_POST, true);
		    curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);				
		    curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt(self::$curl, CURLOPT_TIMEOUT, 60);
			
		    //Get respone
			$response = curl_exec(self::$curl);	
			
			//Check response
			if(empty($response))
			{
				return array();
			}
			
			//Return data
			$response = trim($response);
			$response = Zend_Json::decode($response);
			
			return (($response['error_code'] == 0) ? $response['data'] : array()) ;
		}
		catch (Exception $e)
		{
			
		}
		
		return array();
		
	}
}