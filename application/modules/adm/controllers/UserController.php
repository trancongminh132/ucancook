<?php
class Adm_UserController extends Zend_Controller_Action
{
	
	public function init() 
	{
			 
	}

	/**
	 * Default action
	 */

	public function indexAction()
	{
		$this->_forward('user-list');
	}
	
	public function deleteUserAction()
	{
		Role::isAllowed(Permission::ADMIN_MANAGE,true);
		
		$userId = $this->_getParam('user_id', 0);		 
		$userId = intval($userId);
		 
		$userDetail = Admin::getAdmin($userId);
		 
		if(empty($userDetail))
			$this->_redirect(BASE_URL .'/adm/admin/list');
		$data = array(
			'user_id'	=> $userId,
			'updated_date'	=> time()
		);
		$data['is_locked'] = ($userDetail['is_locked'] == 1) ? 0 : 1;
		$data = array_merge($userDetail,$data);
		$rs = Admin::updateAdmin($data);
		 
		$this->_redirect(BASE_URL .'/adm/admin/list?error=0');
	}	
	
	/**
	 * 
	 * user list
	 */
	public function userListAction()
	{
		Role::isAllowed(Permission::ADMIN_MANAGE,true);
			
		$request = $this->getRequest();
		$limit = 30;
		
		$filters = array();
		
		$email = $this->_getParam('email');
		$email = My_Zend_Globals::strip_word_html($email, '');
		
		if(!empty($email))
		{
			$filters['email'] = $email;
		}

		$isBan4 = $this->_getParam('is_ban');
		$isBan = intval($isBan4);
		
		if(isset($isBan4) && $isBan != 10)
		{
			$filters['is_ban'] = $isBan;
		}
				
		$page = $this->_getParam('page', 1);
		
		$offset = ($page - 1) * $limit;
			
		$listUser = User::getListUser($filters, $offset, $limit);
		
		$total = User::getTotalUser($filters);
		
		$params = array(
			'module' => 'default',
			'controller' => 'user',
			'action' => 'user-list'
		);
		
		$this->view->addHelperPath('My/Helper/', 'My_Helper');
		
		$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], $filters , $total, $page, $limit, PAGE_SIZE, '');
		$this->view->listUser = $listUser;
		$this->view->params = $filters;
	}
	
	/**
	 * edit user
	 * 
	 */
	public function editUserAction()
	{
		Role::isAllowed(Permission::ADMIN_MANAGE,true);
		
		$isUpdate = $this->_getParam('sc', '0');
		 
		$userId = $this->_getParam('id', '0');
		 
		$userDetail = User::getUser($userId);
		 
		if(empty($userDetail)){
			$this->_redirect(BASE_URL. '/adm/user');
		}
		 
		$request = $this->getRequest();
		 
		if($request->isPost())
		{
			$fullName = $request->getParam('full_name','');
			$fullName = My_Zend_Globals::strip_word_html($fullName, '');
		
			$email = $request->getParam('email','');
			$email = My_Zend_Globals::strip_word_html($email, '');
			
			$mobile = $request->getParam('mobile','');
			$mobile = My_Zend_Globals::strip_word_html($mobile, '');
			
			$address = $request->getParam('address','');
			$address = My_Zend_Globals::strip_word_html($address, '');
			
			$gender = $request->getParam('gender','');
			$gender = intval($gender);
			
			$data = array(
				'user_id' 		=>  $userId,
				'display_name'	=>	$fullName,
				'email'			=>  $email,
				'mobile'		=>	$mobile,
				'address'		=>	$address,
				'gender'		=>	$gender
			);
			
			$isUpdate = User::updateUser($data);
			
			$this->_redirect(BASE_URL. '/adm/user/edit-user?user_id='.$userId.'&sc='.$isUpdate);
		}
		 
		$this->view->isUpdate = $isUpdate;
		 
		$this->view->userDetail = $userDetail;
	}
	
	public function lockUnlockAction()
	{
		Role::isAllowed(Permission::ADMIN_MANAGE,true);
	
		$userId = $this->_getParam('user_id', '0');
			
		$userDetail = User::getUser($userId);
			
		if(!empty($userDetail)){
			$isBan = ($userDetail['is_ban'] == 1) ? 0 : 1;
			$userDetail['is_ban'] = $isBan;
			$userDetail['banned_date'] = time();
			$rs = User::updateUser($userDetail);
	
		}
		echo Zend_Json::encode(array('rs' => $rs, 'is_ban' => $isBan));
		die;
	}
}