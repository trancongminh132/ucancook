<?php
class Adm_BannerController extends Zend_Controller_Action
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
		$this->_forward('banner-list');
	}
	
	/**
	 * list banner
	 * 
	 */
	public function bannerListAction()
	{
		Role::isAllowed(Permission::BANNER_LIST, true);
		
		$params = $this->_getAllParams();
		
		$limit = 10;
		
		$page = isset($params['page']) ? intval($params['page']) : 1;
		$offset = ($page - 1) * $limit;
		
		$banners = Banner::getList($params, $offset, $limit);
		$total  = Banner::countTotal($params);
		
		$filters = $params;
		unset($filters['controller']);
		unset($filters['module']);
		unset($filters['action']);
		
		$this->view->addHelperPath('My/Helper/', 'My_Helper');
		$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], $filters, $total, $page, $limit, PAGE_SIZE, "");
		
		$this->view->params = $params;
		$this->view->banners = $banners;
	}
		
	/**
	 *
	 * edit photo action
	 */
	public function editBannerAction()
	{
		Role::isAllowed(Permission::BANNER_EDIT, true);
	
		$id = $this->_getParam('id', 0);
		$id = intval($id);
		
		$banner = Banner::getBanner($id);
		$request = $this->getRequest();
	
		if(empty($banner))
		{
			$this->_redirect(BASE_URL .'/adm/banner/banner-list');
		}
		
		if($request->isPost())
		{
			$urlBanner= $this->_getParam('banner_url');
			
			$order = $this->_getParam('ordering', 1);
			$order = intval($order);
			
			$bannerName= $request->getParam('banner_name');
			$bannerName = My_Zend_Globals::strip_word_html(trim($bannerName), '');
			$bannerName = htmlspecialchars($bannerName);
			
			$positionId = intval($this->_getParam('position_id'));
			
			$link= $request->getParam('link');
			$link = My_Zend_Globals::strip_word_html(trim($link), '');
			$link = htmlspecialchars($link);
			
			if(!empty($urlBanner))
			{
				$data = array(
					'id'	=> $id,
					'banner_url' 	=> $urlBanner,
					'ordering'		=> $order,
					'banner_name'	=> $bannerName,
					'link'			=> $link,
					'position_id'	=> $positionId,
					'created_date'	=> time(),
					'updated_date'	=> time(),
				);
				
				if(Banner::update($data))
				{
					$this->_redirect(BASE_URL .'/adm/banner/banner-list');
				}
			}
		}
	
		$this->view->banner = $banner;
	}
	
	/**
	 *
	 * add photo action
	 */
	public function addBannerAction()
	{
		Role::isAllowed(Permission::BANNER_ADD, true);
	
		$request = $this->getRequest();
		
		if ($this->getRequest()->isPost())
		{			
			$urlBanner= $this->_getParam('banner_url');
			
			$order = $this->_getParam('ordering', 1);
			$order = intval($order);
			
			$bannerName= $request->getParam('banner_name');
			$bannerName = My_Zend_Globals::strip_word_html(trim($bannerName), '');
			$bannerName = htmlspecialchars($bannerName);
			
			$positionId = intval($this->_getParam('position_id'));
			
			$link= $request->getParam('link');
			$link = My_Zend_Globals::strip_word_html(trim($link), '');
			$link = htmlspecialchars($link);
			
			if(!empty($urlBanner))
			{
				$data = array(
					'banner_url' 	=> $urlBanner,
					'ordering'		=> $order,
					'banner_name'	=> $bannerName,
					'link'			=> $link,
					'position_id'	=> $positionId,
					'created_date'	=> time(),
					'updated_date'	=> time(),
				);
				
				$bannerId = Banner::insert($data);
				
				if($bannerId)
				{
					$this->_redirect(BASE_URL .'/adm/banner/banner-list');
				}
			}
		}
	}
	
	/**
	 *
	 * delete banner
	 */
	public function deleteBannerAction()
	{
		Role::isAllowed(Permission::BANNER_DELETE,true);
	
		$id = $this->_getParam('id', 0);
		$id = intval($id);
			
		$banner = Banner::getBanner($id);
		
		if(empty($banner))
		{
			echo 0; exit;
		}
		
		$rs = Banner::delete($id);
		
		echo 1; exit;
	}
	
}