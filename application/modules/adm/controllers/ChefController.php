<?php
class Adm_ChefController extends Zend_Controller_Action
{
	
	public function init() 
	{
		
	}

	/**
	 *
	 * Index action
	 */
	public function indexAction()
	{
		$this->_forward('chef-list');
	}
	
	/**
	 * list chef
	 * 
	 */
	public function chefListAction()
	{
		$params = $this->_getAllParams();
		
		$limit = 10;
		
		$page = isset($params['page']) ? intval($params['page']) : 1;
		$offset = ($page - 1) * $limit;
		
		$chefs = Chef::getList($params, $offset, $limit);
		$total  = Chef::countTotal($params);
		
		$filters = $params;
		unset($filters['controller']);
		unset($filters['module']);
		unset($filters['action']);
		
		$this->view->addHelperPath('My/Helper/', 'My_Helper');
		$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], $filters, $total, $page, $limit, PAGE_SIZE, "");
		
		$this->view->params = $params;
		$this->view->chefs = $chefs;
	}
		
	/**
	 *
	 * edit chef action
	 */
	public function editChefAction()
	{
		$id = $this->_getParam('id', 0);
		$id = intval($id);
		
		$chef = Chef::getChef($id);
		$request = $this->getRequest();
	
		if(empty($chef))
		{
			$this->_redirect(BASE_URL .'/adm/chef/chef-list');
		}
		
		if($request->isPost())
		{
			$chefName= $request->getParam('chef_name');
			$chefName = My_Zend_Globals::strip_word_html(trim($chefName), '');
			$chefName = htmlspecialchars($chefName);
			
			$chefAlias = My_Zend_Globals::aliasCreator($chefName);
			
			$gender = intval($this->_getParam('gender'));
			$avatar = $this->_getParam('avatar');
			
			$description= $request->getParam('chef_description');
			
			if(!empty($chefName) && !empty($description))
			{
				$data = array(
					'id'		=> $id,
					'chef_name'	=> $chefName,
					'chef_alias' => $chefAlias,
					'gender'		=> $gender,
					'avatar'		=> $avatar,
					'chef_description'	=> $description,
					'created_date'	=> time(),
					'updated_date'	=> time()
				);
				
				if(Chef::update($data))
				{
					$this->_redirect(BASE_URL .'/adm/chef/chef-list');
				}
			}
		}
	
		$this->view->chef = $chef;
	}
	
	/**
	 *
	 * add photo action
	 */
	public function addChefAction()
	{
		$request = $this->getRequest();
		
		if ($this->getRequest()->isPost())
		{			
			$chefName= $request->getParam('chef_name');
			$chefName = My_Zend_Globals::strip_word_html(trim($chefName), '');
			$chefName = htmlspecialchars($chefName);
			
			$chefAlias = My_Zend_Globals::aliasCreator($chefName);
			
			$gender = intval($this->_getParam('gender'));
			$avatar = $this->_getParam('avatar');
			
			$description= $request->getParam('chef_description');
			
			if(!empty($chefName) && !empty($description))
			{
				$data = array(
					'chef_name'	=> $chefName,
					'chef_alias' => $chefAlias,
					'gender'		=> $gender,
					'avatar'		=> $avatar,
					'chef_description'	=> $description,
					'created_date'	=> time(),
					'updated_date'	=> time()
				);
				
				$chefId = Chef::insert($data);
				
				if($chefId)
				{
					$this->_redirect(BASE_URL .'/adm/chef/chef-list');
				}
			}
		}
	}
	
	/**
	 *
	 * delete chef
	 */
	public function deleteChefAction()
	{
		$id = $this->_getParam('id', 0);
		$id = intval($id);
			
		$chef = Chef::getChef($id);
		
		if(empty($chef))
		{
			echo 0; exit;
		}
		
		$rs = Chef::delete($id);
		
		echo 1; exit;
	}
	
}