<?php

class CategoryController extends Zend_Controller_Action {

	public function init() {
	
    }
   
	public function detailAction()
    {
    	$offset = 0;
    	$limit = 20;
    	$categoryAlias = $this->_getParam('category_alias', '');
    	$categoryAlias = trim($categoryAlias);
    	
    	$category = Category::selectCategoryByAlias($categoryAlias);
    	$categoryId = $category['category_id'];
		
    	if(empty($category))
    	{
    		$this->_forward('page-not-found', 'error');
			return;
    	}
    	
    	// get navigation path
    	$path = Category::selectSinglePath($categoryId);
    	$categories = array($categoryId);
    	
    	// get children
    	$children = Category::selectChildCategory($categoryId);
    	if(!empty($children))
    	{
    		foreach($children as $cat)
    		{
    			$categories[] = $cat['category_id'];
    		}
    	}
    	
    	$filters = array(
	    	'category_id'	=> $categories,
	    	'product_status' => 1
	    );
	    	
	    $total = Product::countTotal($filters);
	   		
	    $products = array();
	    	
	    if($total > 0)
	    {
	    	$products = Product::getListProductNew($filters, $offset, $limit);
	    	$attributes = array();
	    	foreach($products as $product)
	    	{
	    		if(!isset($attributes[$product['category_id']]))
	    		{
	    			$attributes[$product['category_id']] = Attribute::getListAttributeByCategory($product['category_id']);
	    		}
	    	}
	    }
    	
    	$categoryUrl = Category::categoryUrl($category, true);
    	
    	My_Zend_Globals::setTitle($category['page_title']);
    	My_Zend_Globals::setMeta('keywords', $category['meta_keyword']);
    	My_Zend_Globals::setMeta('description', $category['meta_description']);  
		My_Zend_Globals::setProperty('og:locale', "en_US");
    	My_Zend_Globals::setProperty('og:type', 'category');
    	My_Zend_Globals::setProperty('og:title', $category['page_title']);
    	My_Zend_Globals::setProperty('og:description', $category['meta_description']);
    	My_Zend_Globals::setProperty('og:image', "");
    	My_Zend_Globals::setProperty('og:url', BASE_URL.$categoryUrl);
    	My_Zend_Globals::setProperty('og:site_name', "PhongDoDanOng.com.vn - Trị Xuất Tinh Sớm - Thuốc mọc râu tóc.");
    	
    	$arrayKw = explode(',', $category['meta_keyword']);
    	
    	$this->view->arrayTag = $arrayKw;
    	$this->view->canonicalUrl = BASE_URL.$categoryUrl;
    	$this->view->category = $category;
    	$this->view->path = $path;
    	$this->view->products = $products;
    	$this->view->attributes = $attributes;
    
    }      
}
