<?php
class Adm_ArticleController extends Zend_Controller_Action {
	
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
				
		$articles = Article::getList($params, $offset, $limit, array('get_views' => true, 'order_by' => 'article_id desc'));
		$total = Article::countTotal($params);
		
		$this->view->categoryTree = Category::selectCategoryTree();
		
		$this->view->addHelperPath('My/Helper/', 'My_Helper');
		
		$this->view->params = $params;
		$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], $params, $total, $page, $limit, PAGE_SIZE, "");
		
		$this->view->articles = $articles;
	}

	/**
	 *
	 * Categorycreate action
	 */
	public function createAction() {
		Role::isAllowed(Permission::ARTICLE_ADD, true);

		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();
			 
			$formData['title'] = trim($formData['title']);						
			$formData['title'] = str_replace('"', "'", $formData['title']);
			
			$content = $formData['content'];
			$content = My_Zend_Globals::strip_word_html($content, '<a><h1><h2><h3><style><p><br /><br><strong><i><u><span><b><center><dd><dt><font><img><ul><li><ol><pre><table><td><title><td><tr><tt><object><iframe>');
				
			preg_match_all('/<img[^>]+>/i', $content, $result);
			
			if(count($result[0]) > 0)
			{
				foreach($result[0] as $item)
				{
					preg_match('/src(?: )*=(?: )*[\'"](.*?)[\'"]/i', $item, $images);
			
					if(count($images) > 0)
					{
						$image = $images[1];
			
						if(strpos($image, 'camnanglamme.vn') === false)
						{
							$rs = My_Zend_Globals::leechImage($image, 0);
								
							if(!isset($rs['error_code'])) {
								echo "Leed:". $newImage. "<br/>";
								$newImage = $rs['url'];
								$content = str_replace($image, $newImage, $content);
							}
						}
					}
				}
			}
			
			if (!empty($formData['title'])) {				
				$data = array(
						'title' 			=> $formData['title'],
						'alias'				=> My_Zend_Globals::aliasCreator($formData['title']),
						'description' 		=> trim($formData['description']),
						'content'			=> $content,
						'category_id'		=> intval($formData['category_id']),
						'picture' 			=> trim($formData['picture']),
						'status' 			=> intval($formData['status']),
						'meta_keywords'		=> trim($formData['meta_keywords']),
						'meta_desciption'	=> trim($formData['meta_desciption']),
						'created_date'		=> time(),
						'updated_date'		=> time()
				);

				if( $articleId = Article::insert($data) )
				{
					$tagIds = $formData['tag_id'];
					
					foreach($tagIds as $tagId)
					{						
						$rs = Article::insertArticleTag(array('article_id' => $articleId, 'tag_id' => $tagId));					
					}
					
					$this->_redirect(BASE_URL .'/adm/article/index');
				}
			}
		}
		
		$this->view->categoryTree = Category::selectCategoryTree();
	}

	/**
	 *
	 * Categorycreate action
	 */
	public function editAction() {
		Role::isAllowed(Permission::ARTICLE_EDIT, true);

		$articleId = $this->_getParam('article_id', 0);
		
		if($articleId == 0)
		{
			$this->_redirect(BASE_URL .'/adm/article');
		}
		
		if(!$article = Article::getArticle($articleId))
		{
			$this->_redirect(BASE_URL .'/adm/article');
		}
		
		// get list tags
		$oldTags = Article::getArticleTags($articleId);

		if ($this->getRequest()->isPost()) {
			$formData = $this->_request->getPost();

			$formData['title'] = trim($formData['title']);						
			$formData['title'] = str_replace('"', "'", $formData['title']);

			$tagIds = $formData['tag_id'];
			
			$rs = false;
			
			if(!empty($oldTags) && empty($oldTags))
			{
				$rs = Article::deleteArticleTag($articleId);
			}
			elseif(empty($oldTags) && !empty($tagIds))
			{
				foreach($tagIds as $tagId)
				{
					$rs = Article::insertArticleTag(array('article_id' => $articleId, 'tag_id' => $tagId));
				}
			}
			else
			{
				foreach($oldTags as $tagId)
				{
					if(!in_array($tagId, $tagIds))
					{
						$rs = Article::deleteArticleTag($articleId, $tagId);						
					}
				}
								
				foreach($tagIds as $tagId)
				{
					if(!in_array($tagId, $oldTags))
					{
						$rs = Article::insertArticleTag(array('article_id' => $articleId, 'tag_id' => $tagId));
					}
				}
			}
			
			$content = $formData['content'];
			$content = My_Zend_Globals::strip_word_html($content, '<a><h1><h2><h3><style><p><br /><br><strong><i><u><span><b><center><dd><dt><font><img><ul><li><ol><pre><table><td><title><td><tr><tt><object><iframe>');
			
			preg_match_all('/<img[^>]+>/i', $content, $result);
				
			if(count($result[0]) > 0)
			{
				foreach($result[0] as $item)
				{
					preg_match('/src(?: )*=(?: )*[\'"](.*?)[\'"]/i', $item, $images);
						
					if(count($images) > 0)
					{
						$image = $images[1];

						if(strpos($image, 'camnanglamme.vn') === false)
						{
							$rs = My_Zend_Globals::leechImage($image, 0);
								
							if(!isset($rs['error_code'])) {
								echo "Leed:". $newImage. "<br/>";
								$newImage = $rs['url'];
								$content = str_replace($image, $newImage, $content);
							}
						}
					}
				}
			}
			
			if (!empty($formData['title'])) {				
				$data = array(
						'article_id'		=> $articleId,
						'title' 			=> $formData['title'],
						'alias'				=> My_Zend_Globals::aliasCreator($formData['title']),
						'description' 		=> trim($formData['description']),
						'content'			=> $content,
						'category_id'		=> intval($formData['category_id']),
						'picture' 			=> trim($formData['picture']),
						'status' 			=> intval($formData['status']),
						'meta_keywords'		=> trim($formData['meta_keywords']),
						'meta_desciption'	=> trim($formData['meta_desciption']),					
						'updated_date'		=> time()
				);
				
				if( Article::update($data) )
				{
					$this->_redirect(BASE_URL .'/adm/article/index');
				}
			}
			
			if($rs)
			{
				$this->_redirect(BASE_URL .'/adm/article/index');
			}
		}
		
		$tags = Tag::getList(array('tag_id' => $oldTags));

		$this->view->tags = $tags;
		$this->view->article = $article; 
		$this->view->categoryTree = Category::selectCategoryTree();
	}

	public function deleteAction()
	{
		$articleId = $this->_getParam('article_id', 0);
	
		if($articleId == 0)
		{
			exit;
		}
	
		if(Article::delete($articleId))
		{
			$this->_redirect(BASE_URL . '/adm/article/?delete_success=0');
		}
		else
		{
			$this->_redirect(BASE_URL . '/adm/article/?delete_success=1');
		}
	}
	
	public function addtoblockAction()
	{
		$articleId = $this->_getParam('article_id', 0);
		
		if($articleId == 0)
		{
			$this->_redirect(BASE_URL . '/adm/aritcle');
		}
		
		// get article detail
		$article = Article::getArticle($articleId);
		
		if(empty($article))
		{
			$this->_redirect(BASE_URL . '/adm/aritcle');
		}
		
		if ($this->getRequest()->isPost()) 
		{
			$params = $this->_request->getPost();
			$params['created_date'] = time();
			
			$rs = Article::insertBlockArticle($params);
			
			if($rs)
			{
				$this->_redirect(BASE_URL .'/adm/article/blockarticles/');
			}
			else
			{
				$this->view->message = array('type' => 'error', 'msg' => 'Có lỗi xảy ra, xin hãy kiểm tra lại nội dung.');
			}
		}
		
		$this->view->article = $article;
		$this->view->params = $this->_getAllParams();
	}
	
	public function editblockarticleAction()
	{
		$id = $this->_getParam('id', 0);
	
		if($id == 0)
		{
			$this->_redirect(BASE_URL . '/adm/aritcle/blockarticles');
		}
	
		// get article detail
		$data = Article::getBlockArticle($id);
	
		if(empty($data))
		{
			$this->_redirect(BASE_URL . '/adm/aritcle/blockarticles/');
		}
	
		if ($this->getRequest()->isPost())
		{
			$params = $this->_request->getPost();
			$params['updated_date'] = time();

			$rs = Article::updateBlockArticle($params);
	
			if($rs)
			{
				$this->_redirect(BASE_URL .'/adm/article/blockarticles/');
			}
			else
			{
				$this->view->message = array('type' => 'error', 'msg' => 'Có lỗi xảy ra, xin hãy kiểm tra lại nội dung.');
			}
		}

		//get article info
		$article = Article::getArticle($data['article_id']);
		
		$this->view->data = $data;
		$this->view->article = $article;
	}
	
	public function blockarticlesAction()
	{
		$filters = array();
		$limit = 30;
		
		$params = $this->_getAllParams();
		$page = $this->_getParam('page', 1);
		$blockName = $this->_getParam('block_name', '');
		$status = $this->_getParam('status', -1);
		
		$blockName = trim($blockName);
		
		$offset = ($page - 1) * $limit;
		
		if($blockName != '')
		{
			$filters['block_name'] = $blockName;
		}
		
		if($status != -1)
		{
			$filters['status'] = $status;
		}

		// get block articles		
		$articles = Article::getListBlockArticles($filters, $offset, $limit);
		
		$total = Article::countTotalBlockArticles($filters);
		
		$this->view->addHelperPath('My/Helper/', 'My_Helper');				
		$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], $filters, $total, $page, $limit, PAGE_SIZE, "");
		
		$this->view->articles = $articles;
		$this->view->params = $params;
	}
	
	public function deleteblockarticleAction()
	{
		$id = $this->_getParam('id', 0);
		
		if($id == 0)
		{
			exit;
		}
		
		if(Article::deleteBlockArticle($id))
		{
			$this->_redirect(BASE_URL . '/adm/article/blockarticles?delete_success=0');
		}
		else
		{
			$this->_redirect(BASE_URL . '/adm/article/blockarticles?delete_success=1');
		}
	}	
}