<?php
class ProductController extends Zend_Controller_Action {

	public function init() 
	{
		
    }
    
    public function ingredientAction()
    {
    	$params = $this->_getAllParams();
    	
    	$limit = 12;
    	$page = $this->_getParam('page', 1);
    	$offset = ($page - 1)*$limit;
    	
    	$filters = array();
    	$filters['status'] = 1;
    	$filters['type'] = Category::TYPE_CATEGORY_INGREDIENT;
    	$filters['category_id'] = 0;
    	$filters['sort'] = '';
    	$category = array();
    	
		$categoryList = Category::selectCategoryList($filters, 0, 10);
		$this->view->categoryList = $categoryList;
		
		if(isset($params['category_id']))
		{
			$filters['category_id'] = $params['category_id'];
			$category = Category::selectCategory($params['category_id']);
		}
		
		$filters['type'] = Ingredient::TYPE_SELL;
	
		if(isset($params['sort']))
		{
			$filters['sort'] = $params['sort'];
		}
		
		$ingredient = Ingredient::getListItem($filters, $offset, $limit);
		$total = Ingredient::getTotalIngredient($filters);		
		
		$this->view->addHelperPath('My/Helper/', 'My_Helper');
		 
		$this->view->filters = $filters;
		$this->view->paging = $this->view->paging('/nguyen-lieu.html', $filters, $total, $page, $limit, PAGE_SIZE, '');
		
		$this->view->categoryDetail = $category;
		$this->view->params = $params;
		$this->view->ingredient = $ingredient;
    }
    
    public function ingredientDetailAction()
    {
    	$alias = $this->_getParam('alias');
    	$alias = My_Zend_Globals::strip_word_html($alias, '');
    	
    	$data = Ingredient::getItemByAlias($alias);
    	if(empty($data))
    	{
    		$this->_forward('page-not-found');
    	}
    	
    	$this->view->data = $data;
    	
    	$category = Category::selectCategory($data['category_id']);
    	$this->view->category = $category;
    	
    }
     
	public function sendguestcontactAction()
	{
		$request = $this->getRequest();
	
		$name= $request->getParam('name');
		$name = My_Zend_Globals::strip_word_html(trim($name), '');
		
		$phone= $request->getParam('phone');
		$phone = My_Zend_Globals::strip_word_html(trim($phone), '');
		
		$email= $request->getParam('email');
		$email = My_Zend_Globals::strip_word_html(trim($email), '');
		
		$content= $request->getParam('content');
		$content = My_Zend_Globals::strip_word_html(trim($content), '');
		
		$address= $request->getParam('address');
		$address = My_Zend_Globals::strip_word_html(trim($address), '');
		
		if(!empty($name) && !empty($content) && !empty($email))
		{
			$data = array(
				'name' => $name,
				'phone' => $phone,
				'email'	=> $email,
				'content' => $content,
				'address' => $address,
				'created_date' => time()
			);
			
			$rs = Contact::insert($data);
			
			if($rs)
			{
				echo Zend_Json::encode(array('error_code' => 0));
				exit;
			}
		}
		
		echo Zend_Json::encode(array('error_code' => 1));
		exit;
	}
	
	public function menuAction()
	{
		$topOne = Menu::getListItemByDay(time(), 0, 0, 1);
		
		if(!isset($topOne[0]['alias']))
		{
			$this->_redirect(BASE_URL);
		}
		
		$this->_redirect(BASE_URL.'/thuc-don/'.$topOne[0]['alias'].'.html');	
	}
	
	/**
	 * 
	 * 
	 * menu day
	 */
	public function menuDetailAction()
	{
		$date = NOW;
		
		if(!isset($_COOKIE['delivery_date']) || $_COOKIE['delivery_date'] < NOW)
		{
			$deliveryDate = ProductOrders::beginDate(NOW);
			setcookie("delivery_date", $deliveryDate, time() + 30*24*3600, "/");
		}else{
			$deliveryDate = $_COOKIE['delivery_date'];
		}
			
		$zipCode = isset($_COOKIE['zip_code'])?$_COOKIE['zip_code']:0;
		if(empty($zipCode))
		{
			setcookie("zip_code", 29, time() + 30*24*3600, "/");
		}
		
		$alias = $this->_getParam('product_alias');
		
		$dish = Dish::selectDishByAlias($alias);
		$this->view->dish = $dish;
		
		if(empty($dish))
			$this->_redirect(BASE_URL);
		
		$arrayType = array();
		$dayMenu = Menu::getListItemByDay(NOW, 0, 0, 6);	
		if(!empty($dayMenu))
		{
			foreach($dayMenu as $item)
			{
				$arrayType[] = $item['type'];
			}
			$arrayType = array_unique($arrayType);
		}
		
		$this->view->dayMenu = $dayMenu;
		
		$arrayIngredientIds = explode(',', $dish['ingredient']);
		$listIngre = Ingredient::getMultiIngredient($arrayIngredientIds);
		$this->view->listIngredient = $listIngre;
		
		// get list attribute in category
		$attributes = Attribute::getListAttributeByCategory($dish['type']);
		$this->view->attributes = My_Zend_Globals::myArrayFlip($attributes, 'attribute_id');
		
		//get list value user da chon
		$value = Zend_Json::decode($dish["attributes"]);
		$this->view->value = $value;
		
		$chef = Chef::getChef($dish['chef_id']);
		$this->view->chef = $chef;
		
		$this->view->arrayType = $arrayType;
		
		$thisWeekMenu = Menu::getListItemInWeekOfDish($dish['id'], $date);
		$this->view->thisWeekMenu = $thisWeekMenu;
		
		$nextWeekMenu = Menu::getListItemNextWeekOfDish($dish['id'], 1, $date);
		$this->view->nextWeekMenu = $nextWeekMenu;
		
		$nextTwoWeekMenu = Menu::getListItemNextWeekOfDish($dish['id'], 2, $date);
		$this->view->nextTwoWeekMenu = $nextTwoWeekMenu;
		
		$nextThreeWeekMenu = Menu::getListItemNextWeekOfDish($dish['id'], 3, $date);
		$this->view->nextThreeWeekMenu = $nextThreeWeekMenu;
		
		$this->view->deliveryDate = $deliveryDate;
		$this->view->dateNow = ProductOrders::beginDate(NOW);
	}
	
	/**
	 * 
	 * 
	 * chef detail
	 */
	public function chefDetailAction()
	{
		$chefAlias =$this->_getParam('chef_alias');
		
		$chef = Chef::getChefByAlias($chefAlias);
		$this->view->chef = $chef;
		
		$listDish = Dish::getListDish(array('chef_id' => $chef['id'], 'status' => 1), 0, 100);
		$this->view->listDish = $listDish;
	}
	
	public function setdeliverydateAction()
	{
		$date = $this->_getParam('delivery-date');
		
		setcookie("delivery_date", $date, time() + 30*24*3600, "/");
		
		echo Zend_Json::encode(array('error_code' => 1));
		exit;
	}
}
