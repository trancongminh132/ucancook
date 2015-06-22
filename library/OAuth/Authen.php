<?php
/**
 * OAuth_Authen
 * This class has bean auto_generated at 04/05/2011
 * @author	Truong Dinh Truong<truongtd@vng.com.vn>
 */
class OAuth_Authen {
	/**
	 * authenticated status
	 * @var $_authenticated boolean
	 */
	protected $_authenticated = false;
    /**
     *
     * $userName is name login for user
     */
    public $username;
    
    /**
     *
     * $password is password login for user
     */
    public $password;
    
    /**
     *
     * $foreign_id is id for user google or yahoo
     */
    public $foreign_id = 0;
    
    /**
     *
     * $foreign_type = 2(Google), $foreign_type = 3(Yahoo)
     */
    public $foreign_type = 0;
	
	/**
   	 * Indicates if Cookie support should be enabled.
     */
  	public $cookieSupport = false;
   
    protected static $_instances = array();
    
	/**
     * Constructor of OAuth_Authen
     */
	protected function  __construct() {   
        if(false !== $this->loginWithCookie()){
            $this->_authenticated= true;
        }
        return $this->_authenticated;
   }
   
	/**
     *
     * Get instance of OAuth_Authen
     * @return OAuth_Authen
     */
    public static function getInstance($appId = 0){
    	if (!isset (self::$_instances[$appId]) || null === self::$_instances[$appId]) {
    		self::$_instances[$appId] = new self();    			
    	}
        return self::$_instances[$appId];
    }
    
	/**
	 * Is authenticated
	 * 
	 * @return boolean
	 */
	public function isAuthenticated() {
		return $this->_authenticated;
    }
    
	/**
     * Login OAuth by cookie
     * return boolean
     */
   	public function loginWithCookie(){
   		if( isset($_COOKIE['123mua_authen']) && isset($_COOKIE['123mua_authen'])){
   			$userInfo = explode(':', base64_decode($_COOKIE['123mua_authen']));
   			if(crypt($userInfo[0], 22) === $userInfo[1]) {
   				$fu	= ForeignUsers::findByForeignid($userInfo[0], $userInfo[3]);
   				if($fu->id > 0 && $fu->foreign_password == $userInfo[2]) {
       	 			$this->_authenticated = true;
       	 			$this->foreign_id = $fu->id;
       	 			$this->foreign_type = $fu->foreign_type;
       	 			return true;
       	 		}
   			}
   		}
        return false;
	}
	
	/**
     * Get object model user of this authen object
     * 
     * @return Users
     */
    public function getUser() {
    	if (false == $this->_authenticated) {
    		return false;
    	}
    	ForeignUsers::$table = My_Zend_Globals::getForeignUserPTN($this->foreign_type); 
        return ForeignUsers::retrieveByPk($this->foreign_id, $this->foreign_type);
    }
	
	/**
	 * Set cookie to Browser from session
	 * 
	 */
	public function setCookieFromUser($fu) {
		// create cookies, login				
		$strCookie = base64_encode(join(':',
			array(
				$fu->foreign_id,
				crypt($fu->foreign_id, 22),
				$fu->foreign_password,
				$fu->foreign_type)
			)
		);
		if($this->cookieSupport){
			setcookie('123mua_authen', $strCookie, time()+ 24*3600*30, '/', DOMAIN);
		}else{
			setcookie('123mua_authen', $strCookie, 0, '/', DOMAIN);
		}
	}
	
	/**
	 * Set cookie to Browser
	 * 
	 */
	public function setCookie( $name, $value , $domain, $path='/', $time = '' ){
		if($time == '')
			setcookie($name, $value, time()+ 24*3600*30, $path, $domain);
		else 
			setcookie($name, $value, time()+ $time, $path, $domain);
		//$_COOKIE[$name] = $value;	  
	}
	
	/**
	 * Get cookie from Browser
	 * 
	 */
	public function getCookie($name){
		if (isset($_COOKIE[$name])){
		  return $_COOKIE[$name]; 
		}
		return '';
	}
	
	/**
	 * Delete cookie
	 * 
	 */
	public function removeCookie( $name , $value , $domain, $path='/' ){
		setcookie($name, $value, time() - 24*3600*30, $path, $domain);
	}
	
	public function logout(){
		$this->removeCookie('123mua_authen', '', DOMAIN);
		$this->removeCookie('g_access', '', DOMAIN);
		$this->removeCookie('y_access', '', DOMAIN); 
		unset($_SESSION['foreign_user']);
	}
	public function isLogin(){
		$userInfo = false;		
		
		if(ME_ID > 0){
			$userInfo['user_id'] 	=	ME_ID;
			$userInfo['user_type'] 	= 	1;
		}else{
			if($this->foreign_id){
				$userInfo['user_id'] 	=	$this->foreign_id;
				$userInfo['user_type'] 	= 	$this->foreign_type;
			}
		}
		return $userInfo;
	}
}

?>
