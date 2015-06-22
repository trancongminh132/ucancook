<?php

class UserController extends Zend_Controller_Action 
{
	
	public function init() {
	
    }
	
    public function popupLoginAction()
    {
        header("Access-Control-Allow-Origin: *");
        $this->_helper->layout()->disableLayout();
    }
    
    public function aloginAction() 
    {   	   
    	$auth = Zend_Auth::getInstance();
    	$auth->setStorage(new Zend_Auth_Storage_Session('Default'));
    	
    	if($auth->hasIdentity())
    	{    	
    		$identity = $auth->getIdentity();
    		
    		if($identity->user_id > 0)
    			 $this->view->isPost = true;
    	}
    	
    	$request = $this->getRequest();

		if($request->isPost())
		{
			$email = trim($request->getParam('email', ''));
			$password = trim($request->getParam('password', ''));
			
			$login = User::login($email, $password);
			
			if($login)
    		{
    			 echo'<script> parent.handleSuccessLogin();</script>'; exit;
    		}else{
    			echo'<script>parent.errorLogin();</script>'; exit;
    		}
    	}
    }
    
	public function getlogininfoAction()
    {    	    	
        header("Access-Control-Allow-Origin: *");
    	$arrResult = array('uid' => 0);
    	
    	if (LOGIN_UID > 0) 
    	{
    		$arrResult['uid'] = LOGIN_UID; 		
    	}
    	    	
    	echo Zend_Json::encode($arrResult);
    	exit;
    }
    
    public function registerAction() 
    {    
    	$params = $this->_getAllParams();

    	if ($this->getRequest()->isPost()) 
    	{
    		$params = $this->_request->getPost();
    		
    		$email = isset($params['email_register']) ? $params['email_register'] : '';
    		if(empty($email))
    		{
    			return;
    		}
    		
    		$password = isset($params['password_register']) ? $params['password_register'] : '';
    		$passwordconfirm = isset($params['password_confirm']) ? $params['password_confirm'] : '';
    		
    		$receiveEmail = intval($params['receive_email']);
    		
    		$error = false;
    		$msg = array();
    		
    		if(strlen($password) < 8 || $password !== $passwordconfirm)
    		{
    			$error = true;
    			$msg[] = 'Password too short or do not match.';
    		}
    		
    		if(!$error)
    		{
    			$salt = User::createUserSalt();
    			$password = User::hashPassword($password, $salt);
    			
    			$data = array(				
    				'email'			=> $email,    				
    				'password'		=> $password,
    				'salt'			=> $salt,
                                'registered_date' => time(), 					
    				'last_update'	=> time(),    					
    				'receive_email'	=> $receiveEmail
    			);
    			
    			$userId=  User::insertUser($data);
    			
    			if($userId)
    			{
    				User::setlogin($userId, $password);
    				$this->_redirect(BASE_URL.'/products.html');
    			}else{
    				$this->view->error = 1;
    			}
    		}
    		
    		$this->view->msg = $msg;
    	}
    	
    	$this->view->params = $params;
    	$this->view->noRightSide = true;
    }
    
    public function logoutAction()
    {
    	$auth = Zend_Auth::getInstance();
    	$auth->setStorage(new Zend_Auth_Storage_Session('Default'));
    	 
    	$auth->clearIdentity();
    	 
    	header("Location: ". BASE_URL.'?act=logout');
    	exit;
    }
    
    public function registerFromSocialAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender();
    
    	$request = $this->getRequest();
    
    	$social = strtolower(My_Zend_Globals::strip_word_html($request->getParam('social', 'facebook'), ''));
    	$socialType = '';
		
    	$popup = $request->getParam('popup');
    	
    	switch ($social)
    	{
    		case 'yahoo':
    			$socialType = 'YH';
    			$callbackUrl = $this->_getParam("callback_url");
    			if (!$_GET['oauth_verifier'])
    			{
    				$redirectUrl = BASE_URL . $_SERVER['REQUEST_URI'];
    				$loginUrl = OAuth_Util::getYahooAuthUrl($redirectUrl);
    				$this->_redirect($loginUrl);
    				return;
    			}
    			else
    			{
    				$profile = OAuth_Util::getInfoUserYahoo();
    			}
    			break;
    		case 'google':
    			$profile = OAuth_Util::getInfoUserGoogle();
    			
    			if ( isset($profile['email']) )
    			{
    				$email = $profile['email'];
    				$match = NULL;
    				preg_match("/(.*)@/i", $email, $match);
    				$username = "gg_{$match[1]}";
    			}
    			else
    			{
    				$username = 'gg_' . $profile['name'];
    			}
    			
    			$profile['username'] = $username;
    			$socialType = 'GG';    			
    			$callbackUrl = $_GET['state'];
    			break;
    		case 'facebook':
    		default :
    			$profile = OAuth_Util::getInfoUserFacebook();
    			$callbackUrl = $this->_getParam("callback_url");
    			if ( !$profile )
    			{
    				$redirectUrl = BASE_URL . $_SERVER['REQUEST_URI'];
    				$params = array(
    					'scope' => 'email,user_birthday',
    					'redirect_uri' => $redirectUrl);
    				
    				if ( $this->_getParam("popup") )
    				{
    					$params['display'] = 'popup';
    				}
    				
    				$fbObject = OAuth_Util::getFacebookClient();
    				$loginUrl = $fbObject->getLoginUrl($params);
    				$this->_redirect($loginUrl);
    				return;
    			}
    			
    			$profile['username'] = "fb_" . $profile['username'];
    			
    			$socialType = 'FB';
    			$registerSocialType = 'FB';
    			break;
    	}
    	
    	$userSocialId = $profile['id'];
    	
    	$socialUser = SocialUser::getBySocialId($userSocialId, $socialType);
    	
    	if (empty($socialUser))
    	{
    		$userId = User::insertFromSocialProfile($profile, $userSocialId, $socialType);
    		if($userId)
    		{	
    			$socialUser = array(
    				'user_id' => $userId,
    				'user_social_id' => $profile['id'],
    				'type' => $socialType
    			);
    			SocialUser::insert($socialUser);
    		}
    		else
    		{
    			My_Zend_Logger::log("UserController::registerFromSocialAction() - can not insertFromSocialProfile - socialId: {$profile['id']}");
    			$this->_forward('page-not-found', 'error');
    			return;
    		}
    	}
		
    	$userData = User::getUser(intval($socialUser['user_id']));
    	
    	if(!empty($socialUser['user_id']))
    	{
    		//set login
    		User::setlogin($socialUser['user_id'], $userData['email']);
    	}
    	
    	if(!empty($popup))
    	{
    		echo "<script>window.close();loginaccount.get_login();</script>";
    		exit(0);
    	}
    	else
    	{
    		if($callbackUrl )
    		{
    			$this->_redirect($callbackUrl);
    			return;
    		}
    		
    		$this->_redirect("/");
    		exit(0);
    	}
    }
    
    public function ordersAction()
    {
    	if(LOGIN_UID == 0)
    	{
    		$this->_redirect(BASE_URL);
    	}
    	
    	$filter = array();
    	$filter['buyer_id'] = LOGIN_UID;
    	
    	$page = $this->_getParam('page', 1);
    	$params = $this->_getAllParams();
    	
    	if(!isset($params['order_status'])){
    		$params['order_status'] = 'all';
    	}else{
    		$filter['order_status'] = $params['order_status'];
    	}
    
    	if(isset($params['order_code']) && !empty($params['order_code'])){
    		$filter['order_code'] = $params['order_code'];
    	}
    
    	if(isset($params['order_name']) && !empty($params['order_name'])){
    		$filter['order_name'] = $params['order_name'];
    	}
    
    	if(isset($params['order_phone']) && !empty($params['order_phone'])){
    		$filter['order_phone'] = $params['order_phone'];
    	}
    
    	if(isset($params['order_address']) && !empty($params['order_address'])){
    		$filter['order_address'] = $params['order_address'];
    	}
    	
    	$limit = 30;
    	$offset = ($page - 1) * $limit;
    
    	$listOrders = ProductOrders::getListOrderByUser($filter, $offset, $limit);
    
    	$totalOrder = ProductOrders::getTotalOrder($filter);
    
    	$this->view->listOrders = $listOrders;
    	$this->view->params = $params;
    	
    	$userLogin = User::getUser(LOGIN_UID);
    	$this->view->user = $userLogin; 
    }
    
    public function infoAction()
    {
    	if(LOGIN_UID == 0)
    	{
    		$this->_redirect(BASE_URL);
    	}
    	
    	$dish = Menu::getListItemByDay(time(), 0, 0, 1);
    	
    	if(!isset($dish[0]['alias']))
    	{
    		$dish['url'] = BASE_URL;
    		$dish['image'] = 'http://ucancook.vn/default/images/logo.png';
    	}
    	else{
    		$dish[0]['url'] = BASE_URL.'/thuc-don/'.$dish[0]['alias'].'.html';
    		$dish[0]['image'] = My_Zend_Globals::getThumbImage($dish[0]['image'], 'thumb');
    	}
    	
    	$this->view->dish = $dish[0];
    	$filter = array(
    		'user_id' => LOGIN_UID
    	);
    	
    	$listOrders = ProductOrders::getListOrderByUser($filter, $offset, $limit);
    	
    	$totalOrder = ProductOrders::getTotalOrder($filter);
    	
    	$this->view->listOrders = $listOrders;
    	$this->view->params = $params;
    	
    	$params = array(
    			'module' => 'adm',
    			'controller' => 'order',
    			'action' => 'list'
    	);
    	
    	$this->view->addHelperPath('My/Helper/', 'My_Helper');
    	
    	$this->view->paging = $this->view->paging($params['module'], $params['controller'], $params['action'], 'page', $totalOrder, $page, $limit, PAGE_SIZE, '');
    }
    
    public function profileAction()
    {
    	if(LOGIN_UID == 0)
    	{
    		$this->_redirect(BASE_URL);
    	}
    }
    
    public function shippingAddressAction()
    {
    	if(LOGIN_UID == 0)
    	{
    		$this->_redirect(BASE_URL);
    	}
    	
    	$address = ShippingAddress::getConfig(LOGIN_UID);
    	$this->view->address = $address;
    }
    
    public function tasteAction()
    {
    	if(LOGIN_UID == 0)
    	{
    		$this->_redirect(BASE_URL);
    	}
    	
    	$taste = Dish::getListTasteOfUser(LOGIN_UID);
    	$this->view->taste = $taste;
    }
    
    public function changepasswordAction()
    {
    	$oldPass = $this->_getParam('old_pass');
    	$newPass = $this->_getParam('new_pass');
    	$confirmPass = $this->_getParam('confirm_pass');
    	
    	$user = User::getUser(LOGIN_UID);    	
    	$passwordOldPass = User::hashPassword($oldPass, $user['salt']);
    	
    	if($passwordOldPass != $user['password'] || ($confirmPass != $newPass))
    	{
    		echo Zend_Json::encode(array('error' => 1));die;
    	}
    	
    	$hashNewPass = User::hashPassword($newPass, $user['salt']);
    	
    	$data = array(
    		'user_id' => LOGIN_UID,
    		'password'   => $hashNewPass,
    		'last_update' => time()
    	);
    	
    	$storage = My_Zend_Globals::getStorage();
    	
    	//Update data
    	$rs = $storage->update(User::_TABLE_USER, $data, 'user_id=' . $data['user_id']);
    
    	echo Zend_Json::encode(array('error' => 0));die;
    }
    
    public function changeemailaddressAction()
    {
    	$newEmail = $this->_getParam('new_email');
    	$password = $this->_getParam('password');
    	
    	$user = User::getUser(LOGIN_UID);
    	$hashPassword = User::hashPassword($password, $user['salt']);
    	 
    	if($hashPassword != $user['password'])
    	{
    		echo Zend_Json::encode(array('error' => 1));die;
    	}
    	
    	$userEmail = User::getUserByEmail($newEmail);
    	
    	if(!empty($userEmail))
    	{
    		echo Zend_Json::encode(array('error' => 2));die;
    	}
    	
    	$data = array(
    		'user_id' => LOGIN_UID,
    		'email'   => $newEmail,
    		'last_update' => time()
    	);
    	 
    	$storage = My_Zend_Globals::getStorage();
    	 
    	//Update data
    	$rs = $storage->update(User::_TABLE_USER, $data, 'user_id=' . $data['user_id']);
    
    	echo Zend_Json::encode(array('error' => 0));die;
    }
}
