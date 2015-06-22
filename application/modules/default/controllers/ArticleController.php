<?php

class ArticleController extends Zend_Controller_Action 
{
	public function init() 
	{
		$this->_helper->_layout->setLayout('layout_article');
    }
   
    public function indexAction()
    {
    	// get block articles
        $hotArticles = Article::getListBlockArticles(array('block_name' => 'hot_articles', 'status' => 1), 0, 4);
        $this->view->hotArticles = $hotArticles;

        // news
        $catIds = array('block_homepage_1' => CAT_ID_1, 'block_homepage_2' => CAT_ID_2, 'block_homepage_3' => CAT_ID_3, 'block_homepage_4' => CAT_ID_4);
        
        foreach($catIds as $block => $catId)
        {
        	$children = Category::selectChildCategory($catId);
        	
        	$blockDetail = Article::$_BLOCKS['homepage'][$block];
        	
        	if(!empty($children))
        	{
        		$categoryIds = array();
        		foreach($children as $category)
        		{
        			$categoryIds[] = $category['category_id'];
        		}
        		
        		$blockInfo[$block]['category'] = Category::selectCategory($catId);
        		$blockInfo[$block]['articles'] = Article::getList(array('category_id' => $categoryIds), 0, $blockDetail['limit']);
        	}else{
        		$blockInfo[$block]['category'] = Category::selectCategory($catId);
        		$blockInfo[$block]['articles'] = Article::getList(array('category_id' => $catId), 0, $blockDetail['limit']);
        	}
        }
        //echo"<pre>"; print_r($blockInfo);die;
        $this->view->block = $blockInfo;
        $title = "Ucancook.vn - Chuyên cung cấp thực phẩm chất lượng trên thị trường";
        $desc = "Ucancook.vn - Chuyên cung cấp thực phẩm chất lượng trên thị trường";
                
        My_Zend_Globals::setTitle($title);
        My_Zend_Globals::setMeta('keywords', 'món ngon mỗi ngày, nấu ăn, dạy nấu ăn, hướng dẫn nấu ăn, công thức nấu ăn, cẩm nang nấu ăn, mon ngon moi ngay, nau an, day nau an, huong dan nau an, cong thuc nau an, cam nang nau an');
        My_Zend_Globals::setMeta('description', $desc);
        
        My_Zend_Globals::setProperty('og:title', '');
        My_Zend_Globals::setProperty('og:url', BASE_URL.'/blog');
        My_Zend_Globals::setProperty('og:description', $desc);
        My_Zend_Globals::setProperty('og:image', IMAGES_PATH . '/images/logo.jpg');
    }
    
    public function detailAction()
    {
		$articleId = $this->_getParam('news_id', 0);
    	$articleAlias = $this->_getParam('news_alias', 0);
    	
    	$articleId = intval($articleId);

    	// get article detail
    	$article = Article::getArticle($articleId);

    	if(empty($article) || $article['status'] == 0)
    	{
    		$this->_forward('page-not-found');
    		return;
    	}
    	
    	$categoryId = $article['category_id'];
    	
    	// category
    	$category = Category::selectCategory($categoryId);

    	// get tag
    	$tagIds = Article::getArticleTags($articleId);
    	
    	if(!empty($tagIds))
    	{
    		$tags = Tag::getList(array('tag_id' => $tagIds));
    	}
    	else
    	{
    		$tags = array();
    	}
    	 
    	$relatedArticles = array();
    	
    	// get related articles
    	if(!empty($tags))
    	{
    		$articleIds = Article::getArticleIdByTagId($tagIds, array('exclude_article_id' => array($articleId)), 0, 6);
    		
    		if(!empty($articleIds))
    		{
    			$relatedArticles = Article::getList(array('article_id' => $articleIds), 0, 6);
    		}    		    	  		    
    	}
    	
    	$this->view->tags= $tags;
    	$this->view->article = $article;
    	$this->view->relatedArticles = $relatedArticles;
    	
    	$pageTitle = $article['title'] .' - '. $category['category_name'] .' - Ucancook.vn';
		$keyword = $article['title'];
		
		if(!empty($tags))
		{
			$tmp = array();
			foreach($tags as $tag)
			{
				$tmp[] = $tag['tag_name'];
			}
			
			$keyword = implode(', ', $tmp);
		}
		
		// insert views		
		Article::increaseArticleView($articleId);
		
		$this->view->canonical = Article::articleUrl($article, true);
    	My_Zend_Globals::setTitle($pageTitle);
    	My_Zend_Globals::setMeta('keywords', $keyword);
    	My_Zend_Globals::setMeta('description', $article['description']);

    	My_Zend_Globals::setProperty('og:url', Article::articleUrl($article, true));
    	My_Zend_Globals::setProperty('og:title', $pageTitle);
    	My_Zend_Globals::setProperty('og:description', $article['description']);
    	My_Zend_Globals::setProperty('og:image', $article['picture']);
    	My_Zend_Globals::setProperty('og:type', 'article');
    	$this->view->category = $category;
    }
    
    public function searchAction()
    {
    	$keyword = $this->_getParam('keyword', '');
    	$page = $this->_getParam('page', 1);
    	
    	$keyword = trim($keyword);
    	$keyword = My_Zend_Globals::strip_word_html($keyword, '');

    	if($keyword == '')
    	{
    		$this->_redirect(BASE_URL, array('code' => 301));
    	}
    	
    	$limit = 10;
    	$page = intval($page);
    	if($page == 0) $page = 1;
    	$offset = ($page - 1) * $limit;
    	
    	// get articles
    	$filters = array(
    		'title'		=> $keyword,
    		'status'	=> 1
    	);
    	 
    	$total = Article::countTotal($filters);

    	$articles = array();
    	 
    	if($total > 0)
    	{
    		$articles = Article::getList($filters, $offset, $limit);    	    		
    	}
    	
    	$this->view->articles = $articles;    	
    	
    	$this->view->canonicalUrl = BASE_URL.'/blog/tim-kiem?keyword='.$keyword;
    	$this->view->totalPage = ceil($total/$limit);
    	$this->view->page = $page;
    	
    	$this->view->keyword = $keyword;
    	$pageTitle = 'Các bài viết về '. $keyword .' tại Ucancook - Trang: '. $page;
    	
    	My_Zend_Globals::setTitle($pageTitle);
    	My_Zend_Globals::setMeta('keywords', $keyword);
    	My_Zend_Globals::setMeta('description', '');
    }
    
    public function tagAction()
    {
    	$tagAlias = $this->_getParam('tag_alias', '');
    	$page = $this->_getParam('page', 1);
    	
    	$tagAlias = trim(My_Zend_Globals::strip_word_html($tagAlias, ''));
    	
    	if(empty($tagAlias))
    	{
    		$this->_redirect(BASE_URL);
    	}
    	
    	// get tag info
    	$tag = Tag::getTagByAlias($tagAlias);
    	
    	if(empty($tag))
    	{
    		$this->_forward('page-not-found', 'error');
    		return;
    	}
    	
    	$limit = 20;
    	$page = intval($page);
    	if($page == 0) $page = 1;
    	$offset = ($page - 1) * $limit;
    	
    	// get articles
    	$total = Article::countArticleIdByTagId($tag['id']);
    	
    	if($total > 0)
    	{
    		$articleIds = Article::getArticleIdByTagId($tag['id'], array(), $offset, $limit);
    		$articles = Article::getList(array('article_id' => $articleIds));
    		
    		foreach($articles as &$article)
    		{
    			if(strlen($article['alias']) > 30)
    			{
    				$article['description'] = My_Zend_Globals::cutString($article['description'], 0, 130);
    			}
    			else
    			{
    				$article['description'] = My_Zend_Globals::cutString($article['description'], 0, 175);
    			}
    		}
    	}
    	
    	$pageTitle = $tag['tag_name'] .' - Các chủ đề "'. $tag['tag_name'] .'" tại Ucancook';
    	 
    	$this->view->canonical = Tag::tagUrl($tag, true);
    	
    	My_Zend_Globals::setTitle($pageTitle);
    	My_Zend_Globals::setMeta('keywords', $tag['tag_name']);
    	My_Zend_Globals::setMeta('description', '');
    	
    	My_Zend_Globals::setProperty('og:url', Tag::tagUrl($tag, true));
    	My_Zend_Globals::setProperty('og:title', $pageTitle);    	
    	
    	$this->view->total = $total;
    	$this->view->totalPage = ceil($total/$limit);
    	$this->view->articles = $articles;
    	$this->view->keyword = $tag['tag_name'];
    	$this->view->page = $page;
    }
    
    public function archiveAction()
    {
    	$limit = 10;
    	$page = $this->_getParam('page', 1);
    	$page = intval($page);
    	$offset = ($page - 1)*$limit;
    	
    	$month = $this->_getParam('month', date('n'));
    	$year = $this->_getParam('year', date('Y'));
    	
    	if($month > date('n') && $year > date('Y'))
    		$month = date('n');
    	
    	if($year > date('Y'))
    		$year = date('Y');
		
    	$filters = array(
    		'month' => $month,
    		'year'	=> $year,
    		'status' => 1
    	);
    	
    	$articles = Article::getList($filters, $offset, $limit);
    	$this->view->articles = $articles;
    	
    	$total = Article::countTotal($filters);
    	
    	$this->view->canonicalUrl = BASE_URL.'/blog/archive?month='.$month.'&year='.$year;
    	$this->view->totalPage = ceil($total/$limit);
    	$this->view->page = $page;
    	
    	$this->view->month = $month;
    	$this->view->year = $year;
    }
    
    public function categoryAction()
    {
    	$categoryId = $this->_getParam('category_id');
    	$categoryId = intval($categoryId);
    	
    	$limit = 1;
    	$page = $this->_getParam('page', 1);
    	$page = intval($page);
    	$offset = ($page - 1)*$limit;
    	
    	if(empty($categoryId))
    		$this->_redirect(BASE_URL.'/blog');
    	
    	$category = Category::selectCategory($categoryId);
    	$this->view->category = $category;
    	
    	$filters = array('category_id' => $categoryId, 'status' => 1);
    	
    	$articles = Article::getList($filters, $offset, $limit);
    	$this->view->articles = $articles;

    	$total = Article::countTotal($filters);
    	
    	$canonicalUrl = Category::categoryNewsUrl($category);
    	$this->view->canonicalUrl = $canonicalUrl;
    	
    	$this->view->totalPage = ceil($total/$limit);
    	$this->view->page = $page;
    }
}
