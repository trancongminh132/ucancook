<?php
class Article
{
	const _TABLE = 'articles';
	const _BLOCK_TABLE = 'articles_blocks';
	const _TABLE_ARTICLE_TAG = 'articles_tags';
	const _TABLE_ARTICLE_VIEWS = 'articles_views';
	
	public static $_BLOCKS = array(
		'homepage' => array(
			'hot_articles' =>  array(
				'block_name' =>	'Tin HOT trang chủ (Slider)',
				'image_width'	=> 480,
				'image_height'	=> 302,
				'limit'		=> 5
									),
			'block_homepage_1' =>  array(
				'block_name' =>	'Dạy con',
				'limit'		=> 2
			),
			'block_homepage_2' =>  array(
				'block_name' =>	'Thực đơn cho bé',
				'limit'		=> 5
			),
			'block_homepage_3' =>  array(
				'block_name' =>	'Địa điểm vui chơi',
				'limit'		=> 3
			),
			'block_homepage_4' =>  array(
				'block_name' =>	'Video HOT trên trang chủ',
				'limit'		=> 3
			)
		)
	);
	
	public static function initArticle($data)
	{
		$fields = array('article_id', 'title', 'alias', 'description', 'content', 'category_id', 'picture', 'status', 'meta_keywords', 'meta_description', 'created_date', 'updated_date');

		$rs = array();

		foreach($fields as $field)
		{
			if(isset($data[$field]))
			{
				$rs[$field]  = $data[$field];
			}
		}
		return $rs;
	}
	
	public static function initBlockArticle($data)
	{
		$fields = array('id', 'block_name', 'article_id', 'picture', 'created_date', 'updated_date', 'sort_order', 'status');
	
		$rs = array();
	
		foreach($fields as $field)
		{
			if(isset($data[$field]))
			{
				$rs[$field]  = $data[$field];
			}
		}
		
		return $rs;
	}
	
	public static function initArticleTag($data)
	{
		if(!isset($data['article_id']) || !isset($data['tag_id']))
		{
			return false;
		}
		
		$fields = array('article_id', 'tag_id');
		
		$rs = array();
		
		foreach($fields as $field)
		{
			if(isset($data[$field]))
			{
				$rs[$field]  = $data[$field];
			}
		}
		
		return $rs;
	}
	
	public static function initArticleViews($data)
	{
		if(!isset($data['article_id']) || !isset($data['views']))
		{
			return false;
		}
	
		$fields = array('article_id', 'views');
	
		$rs = array();
	
		foreach($fields as $field)
		{
			if(isset($data[$field]))
			{
				$rs[$field]  = $data[$field];
			}
		}
	
		return $rs;
	}

	/**
	 * Insert article
	 * @param array $data
	 */
	public static function insert($data)
	{
		//Init data
		$data = self::initArticle($data);

		if($data === false)
		{
			return false;
		}

		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			$table = self::_TABLE;
	
			// Insert data
			$rs = $storage->insert($table, $data);
	
			if($rs)
			{
				$rs = $storage->lastInsertId();
				
				// insert table article views
				$data = array(
							'article_id'	=> $rs,
							'views'			=> 0
				);
				
				self::insertArticleViews($data);
			}
	
			// Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::insert - '. $ex->getMessage());
			
			return false;
		}
	}

	/**
	 * Update article
	 * @param array $data
	 */
	public static function update($data)
	{
		try
		{
			$data = self::initArticle($data);
			 
			if($data === false || !isset($data['article_id']))
			{
				return false;
			}
	
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			$table = self::_TABLE;
	
			//Update data
			$rs = $storage->update($table, $data, 'article_id ='. $data['article_id']);
	
			return $rs;
		}
		catch(Exception $ex)
		{			
			My_Zend_Logger::log('Article::update - '. $ex->getMessage());
			
			return false;
		}
	}

	/**
	 * Delete article
	 * @param int $articleId
	 */
	public static function delete($articleId)
	{
		try 
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			$table = self::_TABLE;
	
			//Delete data		
			$rs = $storage->query('DELETE FROM '. self::_TABLE .' WHERE article_id = '. $articleId);
			
			if($rs)
			{
				// delete article views
				$storage->query('DELETE FROM '. self::_TABLE_ARTICLE_VIEWS. ' WHERE article_id = '. $articleId);
				$storage->query('DELETE FROM '. self::_TABLE_ARTICLE_TAG. ' WHERE article_id = '. $articleId);
			}
			
			//Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::delete - '. $ex->getMessage());
			
			return false;
		}
	}

	/**
	 * Select detail of article
	 * @param int $articleId
	 */
	public static function getArticle($articleId)
	{
		if(empty($articleId))
		{
			return false;
		}

		$data = array();
		
		try 
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_TABLE;

			//Query data from database
			$select = $storage->select()
				->from($table, '*')
				->where('article_id = ?', $articleId)			
				->limit(1, 0);
			
			$data = $storage->fetchRow($select);
			
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::selectArticle - '. $ex->getMessage());
			
			return $data;
		}
		
		return $data;
	}
	
	/**
	 * Select detail of article
	 * @param int $articleId
	 */
	public static function getArticleByAlias($alias)
	{
		$alias = trim($alias);
		if(empty($alias))
		{
			return false;
		}
	
		$data = array();
	
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_TABLE;
	
			//Query data from database
			$select = $storage->select()
			->from($table, '*')
			->where('alias = ?', $alias)
			->limit(1, 0);
				
			$data = $storage->fetchRow($select);
				
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::getArticleByAlias - '. $ex->getMessage());
				
			return $data;
		}
	
		return $data;
	}

	/**
	 *
	 * Select list article
	 * @param array $filters
	 */
	public static function getList($filters = array(), $offset = 0, $limit = 25, $options=array('order_by' => 'article_id desc', 'get_views' => false))
	{
		$data = array();
		 
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_TABLE;

			//Query data from database
			$select = $storage->select()
							->from($table, array('article_id', 'title', 'alias', 'description', 'category_id', 'picture', 'status', 'updated_date','created_date'))
							->joinLeft('category', 'category.category_id = '. $table .'.category_id', 'category.category_name')													
							->limit($limit, $offset);
				
			if(isset($options['get_views']) && $options['get_views'])
			{
				$select->joinLeft('articles_views', 'articles_views.article_id = '. $table .'.article_id', 'articles_views.views');
			}
			
			if(isset($filters['category_id']))
			{
				if(is_numeric($filters['category_id']))
				{
					$select->where($table .'.category_id = ?', $filters['category_id']);
				}
				elseif(is_array($filters['category_id']))
				{
					$select->where($table .'.category_id IN (?)', $filters['category_id']);
				}
			}
			
			if(isset($filters['category_not_id']))
			{
				if(is_numeric($filters['category_not_id']))
				{
					$select->where($table .'.category_id <> ?', $filters['category_not_id']);
				}
				elseif(is_array($filters['category_not_id']))
				{
					$select->where($table .'.category_id NOT IN (?)', $filters['category_not_id']);
				}
			}

			if(isset($filters['article_id']))
			{
				if(is_array($filters['article_id']))
				{
					$select->where('article_id IN (?)', $filters['article_id']);
				}
				else
				{
					$select->where('article_id = ?', intval($filters['article_id']));
				}
			}
			
			if(isset($filters['from_article_id']))
			{
				$select->where($table .'.article_id < ?', $filters['from_article_id']);
			}
			
			if(isset($filters['larger_article_id']))
			{
				$select->where($table .'.article_id > ?', $filters['larger_article_id']);
			}

			if(isset($filters['status']) && $filters['status'] > 0)
			{
				$select->where($table .'.status = ?', $filters['status']);
			}
			else
			{
				$select->where($table .'.status = 1');
			}
			
			if(isset($filters['title']))
			{
				$select->where($table .'.title LIKE ?', '%'. $filters['title'] .'%');
			}
			
            if (isset($filters['from_date'])) {
                $select->where($table .'.created_date >= '. $filters['from_date']);
            }
                        
			if(isset($options['order_by']))
			{
				$select->order($options['order_by']);
			}
			
			if(isset($filters['month']) && !empty($filters['month']))
			{
				$select->where('MONTH(FROM_UNIXTIME(articles.created_date)) = ?', $filters['month']);
			}
			
			if(isset($filters['year']) && !empty($filters['year']))
			{
				$select->where('YEAR(FROM_UNIXTIME(articles.created_date)) = ?', $filters['year']);
			}
			
			$data = $storage->fetchAll($select);		
		}
		catch(Exception $ex)
		{			
			My_Zend_Logger::log('Article::getList - '. $ex->getMessage());			
			return false;
		}
		 
		return $data;
	}

	public static function countTotal($filters = array())
	{
		$data = array();

		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_TABLE;
	
			//Query data from database
			$select = $storage->select()
			->from($table, 'count(article_id) as total');
	
			if(isset($filters['category_id']))
			{
				if(is_numeric($filters['category_id']))
				{
					$select->where('category_id = ?', $filters['category_id']);
				}
				elseif(is_array($filters['category_id']))
				{
					$select->where('category_id IN (?)', $filters['category_id']);
				}
			}
							
			if(isset($filters['from_article_id']))
			{
				$select->where($table .'.article_id < ?', $filters['from_article_id']);
			}

			if(isset($filters['larger_article_id']))
			{
				$select->where($table .'.article_id > ?', $filters['larger_article_id']);
			}
			
			if(isset($filters['status']) && $filters['status'] > 0)
			{
				$select->where($table .'.status = ?', $filters['status']);
			}
			else
			{
				$select->where($table .'.status = 1');
			}
				
                        if (isset($filters['from_date'])) {
                            $select->where($table .'created_date >= '. $filters['from_date']);
                        }
                        
			if(isset($filters['title']))
			{
				$select->where('title LIKE ?', '%'. $filters['title'] .'%');
			}
			
			if(isset($filters['month']) && !empty($filters['month']))
			{
				$select->where('MONTH(FROM_UNIXTIME(articles.created_date)) = ?', $filters['month']);
			}
				
			if(isset($filters['year']) && !empty($filters['year']))
			{
				$select->where('YEAR(FROM_UNIXTIME(articles.created_date)) = ?', $filters['year']);
			}
			
			$data = $storage->fetchRow($select);
			
			$data = $data['total'];
		}
		catch(Exception $ex)
		{			
			My_Zend_Logger::log('Article::getList - '. $ex->getMessage());
				
			return false;
		}
			
		return $data;
	}
	
	public static function articleUrl($article, $absolute=false)
	{
		if(empty($article) || !is_array($article))
		{
			return '';
		}

		$url = '/blog/bai-viet/'. $article['alias'] .'_'. $article['article_id'] .'.html';
		
		if($absolute)
			$url = BASE_URL . $url;
		
		return $url;
	}
	
	/**
	 * Select detail of article
	 * @param int $articleId
	 */
	public static function getBlockArticle($id)
	{
		if(empty($id))
		{
			return false;
		}
	
		$data = array();
	
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_BLOCK_TABLE;
	
			//Query data from database
			$select = $storage->select()
						->from($table, '*')
						->where('id = ?', $id)
						->limit(1, 0);
				
			$data = $storage->fetchRow($select);
				
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::getBlockArticle - '. $ex->getMessage());
				
			return $data;
		}
	
		return $data;
	}
	
	/**
	 * Insert article into blocks
	 * @param array $data
	 * @return boolean|unknown
	 */
	public static function insertBlockArticle($data)
	{
		//Init data
		$data = self::initBlockArticle($data);
		
		if($data === false || !isset($data['article_id']) || !isset($data['block_name']))
		{
			return false;
		}
		
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();

			// Insert data
			$rs = $storage->insert(self::_BLOCK_TABLE, $data);
		
			if($rs)
			{
				$rs = $storage->lastInsertId();
			}
		
			// Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::insertToBlock - '. $ex->getMessage());
				
			return false;
		}
	}
	
	/**
	 * Update block article data
	 * @param array $data
	 */
	public static function updateBlockArticle($data)
	{
		try
		{
			$data = self::initBlockArticle($data);
	
			if($data === false || !isset($data['id']))
			{
				return false;
			}
	
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
		
			//Update data
			$rs = $storage->update(self::_BLOCK_TABLE, $data, 'id ='. $data['id']);
	
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::updateBlockArticle - '. $ex->getMessage());
				
			return false;
		}
	}
	
	/**
	 * Delete block article
	 * @param int $id
	 */
	public static function deleteBlockArticle($id)
	{
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			
			//Delete data
			$rs = $storage->query('DELETE FROM '. self::_BLOCK_TABLE .' WHERE id = '. intval($id));
				
			//Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::deleteBlockArticle - '. $ex->getMessage());
				
			return false;
		}
	}
	
	/**
	 *
	 * Select list block articles
	 * @param array $filters
	 */
	public static function getListBlockArticles($filters = array(), $offset = 0, $limit = 25)
	{
		$data = array();
			
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_BLOCK_TABLE;
	
			//Query data from database
			$select = $storage->select()
						->from($table, '*')
						->joinLeft(self::_TABLE, $table .'.article_id = articles.article_id', array('articles.title', 'articles.description', 'articles.alias', 'articles.category_id', 'articles.views', 'articles.updated_date', 'articles.picture'))
						->joinLeft('category', 'category.category_id = articles.category_id', 'category.category_name')
						->limit($limit, $offset);
	
			if(isset($filters['block_name']) && $filters['block_name'] != '')
			{				
				$select->where($table .'.block_name = ?', trim($filters['block_name']));						
			}
				
			if(isset($filters['article_id']))
			{
				$select->where($table .'.article_id = ?', $filters['article_id']);
			}
	
			if(isset($filters['status']))
			{
				$select->where($table .'.status = ?', $filters['status']);
			}
		
			$select->order('block_name asc');
			$select->order('sort_order asc');
			$select->order('created_date desc');

			$data = $storage->fetchAll($select);
			
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::getListBlockArticles - '. $ex->getMessage());				
			return false;
		}
			
		return $data;
	}
	
	public static function countTotalBlockArticles($filters = array())
	{
		$data = array();
			
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			$table = self::_BLOCK_TABLE;
	
			//Query data from database
			$select = $storage->select()
						->from($table, 'count(id) as total');
	
			if(isset($filters['block_name']) && $filters['block_name'] != '')
			{				
				$select->where($table .'.block_name = ?', trim($filters['block_name']));						
			}
				
			if(isset($filters['article_id']))
			{
				$select->where($table .'.article_id = ?', $filters['article_id']);
			}
	
			if(isset($filters['status']))
			{
				$select->where($table .'.status = ?', $filters['status']);
			}
				
			$data = $storage->fetchRow($select);
				
			$data = $data['total'];
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::countTotalBlockArticles - '. $ex->getMessage());
	
			return false;
		}
			
		return $data;
	}
	
	/**
	 * Insert tag of article
	 * @param array $data
	 * @return boolean
	 */
	public static function insertArticleTag($data)
	{
		//Init data
		$data = self::initArticleTag($data);
	
		if($data === false)
		{
			return false;
		}
	
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			// Insert data
			$rs = $storage->insert(self::_TABLE_ARTICLE_TAG, $data);
			
			// Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::insertArticleTag - '. $ex->getMessage());
	
			return false;
		}
	}
	
	/**	 
	 * Select list tags of article
	 * @param int $articleId
	 */
	public static function getArticleTags($articleId)
	{
		$data = array();
			
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			
			//Query data from database
			$select = $storage->select()
							->from(self::_TABLE_ARTICLE_TAG, 'tag_id')
							->where('article_id = ?', $articleId);			

			$data = $storage->fetchAll($select);
			
			if(!empty($data))
			{
				$tmp = array();
				foreach($data as $item)
				{
					$tmp[] = $item['tag_id'];
				}
				
				$data = $tmp;
				unset($tmp);
			}
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::getArticleTags - '. $ex->getMessage());
	
			return false;
		}
			
		return $data;
	}
	
	/**
	 * get list article id by tag id
	 * @param int $tagId
	 * @return boolean|multitype:unknown
	 */
	public static function getArticleIdByTagId($tagId, $options = array(), $offset = 0, $limit = 30)
	{
		$data = array();
			
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
				
			//Query data from database
			$select = $storage->select()->distinct()
						->from(self::_TABLE_ARTICLE_TAG, 'article_id')
						->order('article_id desc')
						->limit($limit, $offset);

			if(is_array($tagId))
			{
				$select->where('tag_id IN (?)', $tagId);
			}
			else
			{
				$select->where('tag_id = ?', intval($tagId));
			}
			
			if(isset($options['exclude_article_id']) && is_array($options['exclude_article_id']))
			{
				$select->where('article_id NOT IN (?)', $options['exclude_article_id']);
			}
			
			$data = $storage->fetchAll($select);
				
			if(!empty($data))
			{
				$tmp = array();
				foreach($data as $item)
				{
					$tmp[] = $item['article_id'];
				}
	
				$data = $tmp;
				unset($tmp);
			}
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::getArticleIdByTagId - '. $ex->getMessage());
	
			return false;
		}
			
		return $data;
	}
	
	public static function countArticleIdByTagId($tagId, $options = array())
	{
		$data = array();
			
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
			
			//Query data from database
			$select = $storage->select()
					->from(self::_TABLE_ARTICLE_TAG, 'count(article_id) as total');					
			
			if(is_array($tagId))
			{
				$select->where('tag_id IN (?)', $tagId);
			}
			else
			{
				$select->where('tag_id = ?', intval($tagId));
			}
			
			if(isset($options['exclude_article_id']) && is_array($options['exclude_article_id']))
			{
				$select->where('article_id NOT IN (?)', $options['exclude_article_id']);
			}
			
			$data = $storage->fetchRow($select);
	
			$data = $data['total'];
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::countArticleIdByTagId - '. $ex->getMessage());
	
			return false;
		}
			
		return $data;
	}
	
	/**
	 * Delete article tag
	 * @param int $articleId
	 * @param int $tagId
	 */
	public static function deleteArticleTag($articleId, $tagId = 0)
	{
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
				
			//Delete data
			if($tagId > 0)
			{
				$rs = $storage->query('DELETE FROM '. self::_TABLE_ARTICLE_TAG .' WHERE article_id = '. intval($articleId) .' AND tag_id='. intval($tagId));
			}
			else
			{
				$rs = $storage->query('DELETE FROM '. self::_TABLE_ARTICLE_TAG .' WHERE article_id = '. intval($articleId));
			}
			
			//Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::deleteArticleTag - '. $ex->getMessage());
	
			return false;
		}
	}
	
	/**
	 * Select article pageviews
	 * @param int $articleId
	 */
	public static function getArticleViews($articleId)
	{
		if(empty($articleId))
		{
			return false;
		}

		$views = 0;
		
		try 
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();		

			//Query data from database
			$select = $storage->select()
				->from(self::_TABLE_ARTICLE_VIEWS, '*')
				->where('article_id = ?', $articleId)			
				->limit(1, 0);
			
			$data = $storage->fetchRow($select);
			
			if(isset($data['views']))
			{
				$views = $data['views'];
			}
			
			return $views;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::getArticleViews - '. $ex->getMessage());
			
			return $views;
		}
		
		return $data;
	}
	
	/**
	 * Insert article views
	 * @param array $data
	 */
	public static function insertArticleViews($data)
	{
		//Init data
		$data = self::initArticleViews($data);
	
		if($data === false)
		{
			return false;
		}
	
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();

			// Insert data
			$rs = $storage->insert(self::_TABLE_ARTICLE_VIEWS, $data);
				
			// Return
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::insertArticleViews - '. $ex->getMessage());
				
			return false;
		}
	}
	
	/**
	 * Increase views
	 * @param array $data
	 */
	public static function increaseArticleView($articleId, $views=1)
	{
		try
		{
			$articleId = intval($articleId);
			
			if($articleId == 0)
			{
				return false;
			}
			
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			$data = array(					
						'views'		=> new Zend_Db_Expr('views + '. $views),
						'last_view'	=> time()
			);
	
			//Update data
			$rs = $storage->update(self::_TABLE_ARTICLE_VIEWS, $data, 'article_id ='. $articleId);
	
			return $rs;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::update - '. $ex->getMessage());
				
			return false;
		}
	}
	
	/**
	 * get article most views
	 * @param int $offset, $limit
	 */
	public static function getArticleMostView($offset = 0, $limit = 3)
	{
		try
		{
			//Get db instance
			$storage = My_Zend_Globals::getStorage();
	
			//Query data from database
			$select = $storage->select()
							->from(self::_TABLE_ARTICLE_VIEWS, 'article_id')
							->order('views DESC')
							->limit($limit, $offset);
				
			$articleIds = $storage->fetchCol($select);
			
			$dataReturn = array();
			
			if(!empty($articleIds))
			{
				$dataReturn = self::getList(array('article_id' => $articleIds, 'status' => 1), 0, 3);
			}
			
			return $dataReturn;
		}
		catch(Exception $ex)
		{
			My_Zend_Logger::log('Article::getArticleMostView - '. $ex->getMessage());				
			return $views;
		}
			
		return $data;
	}
}
