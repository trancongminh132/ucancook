<?php
class Adm_TagController extends Zend_Controller_Action {
	
	public function init() {
		
	}

	/**
	 *
	 * Index action
	 */
	public function indexAction()
	{
		$params = $this->_getAllParams();
		
		$limit = 30;
		
		$page = isset($params['page']) ? intval($params['page']) : 1;
		$offset = ($page - 1) * $limit;
				
		$tags = Tag::getList($params, $offset, $limit);
		$total = Tag::countTotal($params);
	
		$this->view->addHelperPath('My/Helper/', 'My_Helper');
		
		$this->view->params = $params;
		$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], $filters, $total, $page, $limit, PAGE_SIZE, "");
		$this->view->tags = $tags;
	}

	/**
	 *
	 * Name create action
	 */
	public function createAction() {
		Role::isAllowed(Permission::TAG_ADD, true);

		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			 
			$formData['tag_name'] = trim(htmlspecialchars($formData['tag_name']));
			
			if (!empty($formData['tag_name'])) {				
				$data = array(
						'tag_name' 			=> $formData['tag_name'],
						'tag_name_ascii' 	=> My_Zend_Globals::utf8ToAscii($formData['tag_name'], ' '),
						'tag_alias' 		=> My_Zend_Globals::aliasCreator($formData['tag_name'])					
				);
							
				if( Tag::insert($data) )
				{
					$this->_redirect(BASE_URL .'/adm/tag/index');
				}
			}
		}		
	}

	/**
	 *
	 * name create action
	 */
	public function editAction() {
		Role::isAllowed(Permission::TAG_EDIT, true);

		$id = $this->_getParam('id', 0);
		
		if($id == 0)
		{
			$this->_redirect(BASE_URL .'/adm/tag');
		}
		
		if(!$tag = Tag::getTag($id))
		{
			$this->_redirect(BASE_URL .'/adm/tag');
		}
		
		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			 
			$formData['tag_name'] = trim(htmlspecialchars($formData['tag_name']));
			
			if (!empty($formData['tag_name'])) {				
				$data = array(
						'id'				=> $id,
						'tag_name' 			=> $formData['tag_name'],
						'tag_name_ascii' 	=> My_Zend_Globals::utf8ToAscii($formData['tag_name'], ' '),
						'tag_alias' 		=> My_Zend_Globals::aliasCreator($formData['tag_name'])		
				);

				if( Tag::update($data) )
				{
					$this->_redirect(BASE_URL .'/adm/tag/index');
				}
			}
		}
		
		$this->view->tag = $tag; 		
	}


	/**
	 *
	 * name delete action
	 */
	public function deleteAction() {
		Role::isAllowed(Permission::TAG_DELETE, true);

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$id = intval($this->_getParam('id', 0));

		$flag = Tag::delete($id);
		
		if($flag)
		{
			$this->_redirect(BASE_URL .'/adm/tag/index?error=0');
		}
				
		$this->_redirect(BASE_URL .'/adm/tag/index?error=1');
	}
	
	/**
	 *
	 * Index action
	 */
	public function ajaxSearchAction()
	{
		$tagName = $this->_getParam('term');
	
		$limit = 20;
	
		$filters['tag_name'] = trim(My_Zend_Globals::strip_word_html($tagName, ''));
		$tags = Tag::getList($filters, $offset, $limit);
		
		if(!empty($tags))
		{
			$rt = array();
			foreach ($tags as $tag)
			{
				$rt[] = array(
						'id'		=> $tag['id'],
						'value'	=> $tag['tag_name']
				);
			}
			
			$tags = $rt;
			unset($rt);
		}
		
		echo Zend_Json::encode($tags);
		exit;
	}
	
	public function ajaxAddTagAction()
	{
		$tagName = $this->_getParam('tag_name');
		
		$tagId = 0;
		
		if(!empty($tagName))
		{
			$tagName = My_Zend_Globals::strip_word_html(trim($tagName), '');
			
			$data = array(
				'tag_name' 			=> $tagName,
				'tag_name_ascii' 	=> My_Zend_Globals::utf8ToAscii($tagName, ' '),
				'tag_alias' 		=> My_Zend_Globals::aliasCreator($tagName)
			);
			
			if( $tagId = Tag::insert($data) )
			{
				
			}
			elseif($tag = Tag::getTagByAlias(My_Zend_Globals::aliasCreator($tagName)))
			{
				$tagId = $tag['id'];
				$tagName = $tag['tag_name'];
			}
		}
		
		echo Zend_Json::encode(array('tag_id' => $tagId, 'tag_name' => $tagName));
		exit;
	}
}