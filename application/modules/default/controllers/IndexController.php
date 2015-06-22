<?php

class IndexController extends Zend_Controller_Action {

	public function init() {
		
    }

    /**
     *
     * Default action
     */
    public function indexAction() 
    {    	
    	
    	$title = "Ucancook.vn - Chuyên cung cấp thực phẩm chất lượng trên thị trường";
    	$desc = "Ucancook.vn - Chuyên cung cấp thực phẩm chất lượng trên thị trường";
    	
        My_Zend_Globals::setTitle($title);
    	My_Zend_Globals::setMeta('keywords', "món ngon mỗi ngày, nấu ăn, dạy nấu ăn, hướng dẫn nấu ăn, công thức nấu ăn, cẩm nang nấu ăn, mon ngon moi ngay, nau an, day nau an, huong dan nau an, cong thuc nau an, cam nang nau an");
    	My_Zend_Globals::setMeta('description', $desc);  
		My_Zend_Globals::setProperty('og:locale', "en_US");
    	My_Zend_Globals::setProperty('og:type', 'index');
    	My_Zend_Globals::setProperty('og:title', $title);
    	My_Zend_Globals::setProperty('og:description', $desc);
    	My_Zend_Globals::setProperty('og:image', "");
    	My_Zend_Globals::setProperty('og:url', BASE_URL);
    	My_Zend_Globals::setProperty('og:site_name', $title);
    	
    	$this->view->canonicalUrl = BASE_URL;
    	$this->view->banner = $arrayBanner;
    }
	
    /**
     * 
     * about us action
     */
    public function aboutUsAction()
    {
    	
    }
    
    /**
     * 
     * how work
     */
	public function howWorkAction()
    {
    	
    }
    
    /**
     *
     *login
     */
    public function loginAction()
    {
    	if(LOGIN_UID > 0)
    		$this->_redirect(BASE_URL);
    	
    	 $return = $this->_getParam('return', '');
    	 $this->view->return = $return;
    }
    
    /**
     *
     * register
     */
    public function registerAction()
    {
    	if ( LOGIN_UID > 0)
    	{
    		$this->_redirect(BASE_URL);
    	}
    	
    	$request = $this->getRequest();
    	
    	$stepRegister = 1;
    	
    	$isError = false;
    	$params = array(
    		'fullname' => '',
    		'email' => ''
    	);
    	 
    	if($request->isPost())
    	{
    		$this->view->isPost = true;
    	
    		$formParams = $this->_getRegisterFormParams();
    		$isError = $formParams['errorCode'];
    		$params = $formParams['params'];
    	
    		$email = $params['email'];
    		$checkEmail = User::getUserByEmail($email);
    		if(!empty($checkEmail))
    		{	
    			$isError = 8;
    		}
    		
    		if(!$isError)
    		{
    			$ip = My_Zend_Globals::getAltIp();
    			$fullname = isset($params['fullname'])?$params['fullname']:'';
    			$email = $params['email'];
    			$password = $params['password'];
    			$cityId = $params['city'];
    			$districtId = $params['district'];
    			 
    			$salt = User::createUserSalt();
    			$password = User::hashPassword($password, $salt);
    			 
    			$data = array(
    				'display_name' => $fullname,
    				'email'			=> $email,
    				'password'		=> $password,
    				'salt'			=> $salt,
    				'registered_date' => time(),
    				'last_update'	=> time(),
    				'receive_email'	=> 1,
    				'city_id'	=> $cityId,
    				'district_id' => $districtId
    			);
    			 
    			// delay startup user. Will startup when user login first times
    			$userId=  User::insertUser($data);
    	
    			if($userId && (!$isError))
    			{
    				User::setlogin($userId, $password);
    				$this->_redirect(BASE_URL);
    			}
    		}
    		
    		$this->view->isError = $isError;
    	}
    }
    
    /**
     * validate register form and return params
     * @return ArrayObject contains errorCode and params
     */
    private function _getRegisterFormParams()
    {
    	$params = array();
    	$request = $this->getRequest();
    	
    	$email = trim(My_Zend_Globals::strip_word_html($request->getParam('email', ''), ''));
    	$isError = empty($email) ? 2 : $isError;
    	$isError = !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email) ? $isError = 2 : $isError;
    	$params['email'] = $email;
    
    	$password = My_Zend_Globals::strip_word_html($request->getParam('password', ''), '');
    	$isError = empty($password) ? 4 : $isError;
    	$specialChars = '~!@#$%^&*()-+';
    	$isError = strpbrk($password, $specialChars) == false ? 5 : $isError;
    	$params['password'] = $password;
    
    	$rePassword = My_Zend_Globals::strip_word_html($request->getParam('repassword', ''), '');
    	$isError = ($rePassword == '' || $password != $rePassword) ? 7 : $isError;
    	 
    	$city = trim(My_Zend_Globals::strip_word_html($request->getParam('city', ''), ''));
    	$isError = empty($city) ? 3 : 0;
    	$params['city'] = $city;
    	
    	$params['district'] = $request->getParam('district');
    	
    	return array(
    		'errorCode' => $isError,
    		'params' => $params
    	);
    }
    
    /**
     *
     * term & condition
     */
    public function termAction()
    {
    
    }
    
    /**
     *
     * privacy
     */
    public function privacyAction()
    {
    	
    }
}
