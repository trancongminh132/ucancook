<?php
class Adm_CategoryController extends Zend_Controller_Action {

	public function init() 
	{
		
	}

	/**
	 *
	 * Index action
	 */
	public function indexAction()
	{
		$this->_forward('category-list');
	}

	/**
	 *
	 * category list action
	 */
	public function categoryListAction() 
	{
		$limit = 10;
		$page = $this->_getParam('page', 1);
		$offset = ($page -1)*$limit;
		
		Role::isAllowed(Permission::CATEGORY_VIEW, true);

		$params = $this->_getAllParams();
		$categoryId = intval($this->_getParam('id', 0));
		
		$arrMenuCategory = Category::selectCategoryTree();
		
		$filters = array();
		$filters['parent_id'] = $categoryId;
		
		if(isset($params['category_name']) && !empty($params['category_name']))
		{
			$filters['category_name'] = $params['category_name'];
		}
		
		if(isset($params['type']) && !empty($params['type']))
		{
			$filters['type'] = $params['type'];
		}
		
		$arrCategory = Category::selectCategoryList($filters, $offset, $limit);
		
		$arrDetail = Category::selectCategory($categoryId);

		$total = Category::countCategory($filters);
		
		$this->view->addHelperPath('My/Helper/', 'My_Helper');
		
		$this->view->params = $params;
		$this->view->arrCategory = $arrCategory;
		$this->view->arrMenuCategory = $arrMenuCategory;
		$this->view->id = $categoryId;
		$this->view->arrDetail = $arrDetail;
		$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], array(), $total, $page, $limit, PAGE_SIZE, "");
	}

	/**
	 *
	 * Categorycreate action
	 */
	public function addCategoryAction() 
	{
		Role::isAllowed(Permission::CATEGORY_ADD, true);
		
		$flag = 0;

		if ($this->getRequest()->isPost()) 
		{
			$formData = $this->_request->getPost();
			
			if (!empty($formData['category_name'])) 
			{
				$formData['category_alias'] = My_Zend_Globals::aliasCreator($formData['category_name']);
				
				$data = array(
					'category_name' 	=> $formData['category_name'],
					'category_alias'	=> $formData['category_alias'],
					'type'				=> $formData['type'],
					'shipping_cost'		=> $formData['shipping_cost'],
					'page_title' 		=> $formData['page_title'],
					'meta_keyword'		=> $formData['meta_keyword'],
					'meta_description'	=> $formData['meta_description'],
					'parent_id' 		=> intval($formData['parent_id']),
					'ordering' 			=> intval($formData['ordering']),
					'status'			=> intval($formData['status']),
					'show_menu'			=> intval($formData['show_menu']),
					'created_date'		=> time(),
					'updated_date'		=> time()
				);
				
				if(Category::insertCategory($data))
				{
					$this->_redirect(BASE_URL .'/adm/category/category-list');
				}
			}
		}

		$arrMenuCategory = Category::selectCategoryTree();
		$arrParent = Category::selectCategoryList(array('parent_id' => 0));
		
		$this->view->arrMenuCategory = $arrMenuCategory;
		$this->view->arrParent = $arrParent;
		$this->view->flag = $flag;
	}

	/**
	 *
	 * Categoryedit action
	 */
	public function editCategoryAction() 
	{
		Role::isAllowed(Permission::CATEGORY_EDIT, true);

		$categoryId = intval($this->_getParam('id', 0));
		$arrCategory = array();
		$parent_id = 0;
		$flag = 0;

		if ($categoryId > 0) 
		{
			// Cap nhat
			if ($this->getRequest()->isPost()) 
			{
				$formData = $this->_request->getPost();
				$categoryId = $formData['id'];
				$parent_id = $formData['parent_id'];

				if (!empty($formData['category_name'])) 
				{					
					$formData['category_alias'] = My_Zend_Globals::aliasCreator($formData['category_name']);
											
					$data = array(
						'category_id' 		=> $categoryId,
						'category_name' 	=> $formData['category_name'],
						'category_alias'	=> $formData['category_alias'],
						'type'				=> $formData['type'],
						'shipping_cost'		=> $formData['shipping_cost'],
						'page_title' 		=> $formData['page_title'],
						'meta_keyword'		=> $formData['meta_keyword'],
						'meta_description'	=> $formData['meta_description'],
						'parent_id' 		=> $parent_id,
						'ordering' 			=> intval($formData['ordering']),
						'status'			=> intval($formData['status']),
						'show_menu'			=> intval($formData['show_menu']),
						'updated_date'		=> time()
					);
					
					if(Category::updateCategory($data))
					{
						$this->_redirect(BASE_URL .'/adm/category/category-list');
					}						
				}				
			}
			// End cap nhat

			$arrCategory = Category::selectCategory($categoryId);
			
			if(!empty($arrCategory))
			{
				$parent_id = $arrCategory['parent_id'];
			}
			
		} 
		else 
		{
			$this->_redirect('/adm/category/category-list');
		}

		$arrParent = Category::selectCategoryList(array('parent_id' => 0));
		
		$arrMenuCategory = Category::selectCategoryTree();
		
		$this->view->parent_id = $parent_id;
		$this->view->flag = $flag;

		$this->view->arrCategory = $arrCategory;
		$this->view->arrParent = $arrParent;
		$this->view->arrMenuCategory = $arrMenuCategory;
	}

	/**
	 *
	 * delete category action
	 */
	public function deleteCategoryAction() 
	{
		Role::isAllowed(Permission::CATEGORY_DELETE, true);

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$categoryId = intval($this->_getParam('id', 0));
		$flag = 0;

		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			if ($formData['id'] > 0 && $formData['act'] == 'del') {
				
				// Xoa danh muc con
				$arrCate = Category::selectCategoryList(array('parent_id' => $formData['id']));
				if (is_array($arrCate) && !empty($arrCate)) {
					foreach ($arrCate as $value) {
						if ($value['category_id'] > 0) {
							Category::deleteCategory($value['category_id']);
						}
					}
				}			
				// Xoa danh muc cha
				Category::deleteCategory($formData['id']);
				//End
			}
		}

		echo $flag;exit;
	}

	/**
	 *
	 * Categorypublish action
	 */
	public function updateStatusCategoryAction() 
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$categoryId = intval($this->_getParam('id', 0));
		$flag = 0;

		$status = $this->_getParam('status', 0);
		$status = intval($status);
		
		if ($categoryId > 0) 
		{
			$data['category_id'] = $categoryId;
			$data['status'] = $status;
			$data['updated_date'] = time();
			$flag = Category::updateCategory($data);
		}
		
		if($flag)
			$this->_redirect(BASE_URL.'/adm/category?result=true');
		else
			$this->_redirect(BASE_URL.'/adm/category?result=false');
	}

}