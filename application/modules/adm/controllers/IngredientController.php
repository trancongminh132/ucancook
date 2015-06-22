<?php
class Adm_IngredientController extends Zend_Controller_Action
{   	
    public function init()
    {    	   	
    	
    }
	
	/**
	*
	*	index action
	*
	*/	
	public function indexAction()
    {
		$this->_forward('ingredient-list');
	}
	
    /**
	*
	*	ingredient list action
	*
	*/
    public function ingredientListAction()
    {    	
    	$params = $this->_getAllParams();
    	
    	$page = $this->_getParam('page', 1);   	
    	$limit = 20;    	
    	$offset = ($page - 1) * $limit;
    	
    	$filters = array();
    	
    	if(isset($params['ingredient_name']) && !empty($params['ingredient_name']))
    	{
    		$filters['ingredient_name'] = $params['ingredient_name'];
    	}
    	
    	if(isset($params['type']) && !empty($params['type']))
    	{
    		$filters['type'] = $params['type'];
    	}
    	
    	if(isset($params['category_id']) && !empty($params['category_id']))
    	{
    		$filters['category_id'] = $params['category_id'];
    	}
    	
    	$total = Ingredient::getTotalIngredient($filters);    	
    	$ingredients = Ingredient::getListItem($filters, $offset, $limit);
    	
    	$this->view->addHelperPath('My/Helper/', 'My_Helper');
        $this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], array(), $total, $page, $limit, PAGE_SIZE, "");
       	
        $this->view->ingredients = $ingredients;
		$this->view->params = $params;
		
		$categoryList = Category::selectCategoryList(array('status' => 1, 'type' => Category::TYPE_CATEGORY_INGREDIENT), 0, 30);
		$this->view->categoryList = $categoryList;
		
    	My_Zend_Globals::setTitle('Quản lý món ăn');
    }
	
	/**
	*
	* add ingredient action
	*
	*/ 
	public function addIngredientAction()
    {
    	$request = $this->getRequest();
    	$error = 0;   	
    	if($request->isPost())
    	{    
    		$ingredientName = $request->getParam('ingredient_name', '');   		
    		$ingredientName = My_Zend_Globals::strip_word_html($ingredientName,'');
    		
    		$alias = My_Zend_Globals::aliasCreator($ingredientName);
    		
			$unitPrice = $request->getParam('unit_price','');   		
    		$unitPrice = intval($unitPrice);
			
    		$type = $request->getParam('type','');
    		$type = intval($type);
    		
    		$categoryId = $request->getParam('category_id','');
    		$categoryId = intval($categoryId);
    		
			$image = $request->getParam('ingredient_image', '');   		
    		$image = My_Zend_Globals::strip_word_html($image,'');
			
    		$price = $request->getParam('price','');   		
    		$price = floatval(str_replace('.', '', $price));
    	
			$specialPrice = $request->getParam('special_price','');   		
    		$specialPrice = floatval(str_replace('.', '', $specialPrice));
			
			$quantity= $request->getParam('quantity','');   		
    		$quantity = intval($quantity);
			
			$description = $request->getParam('description', '');   
			
			//summary
			$summary = $request->getParam('summary');
			$summary = My_Zend_Globals::strip_word_html(trim($summary), '');
			$summary = htmlspecialchars($summary);
			
			$pictures = $request->getParam('pic_url');
			$n = count($pictures);
			
			for($i = 0; $i < $n; $i++ )
			{
				$picPro[] = $pictures[$i];
			}
			
			if(empty($image))
			{
				if(!empty($pictures))
				{
					$image = $pictures[0];
				}
			}
			
    		$data = array(	
				'name' => $ingredientName, 
				'unit_price' => $unitPrice, 
    			'type' => $type,
    			'alias' => $alias,
    			'category_id' => $categoryId,
				'price'	=> $price,
				'special_price' => $specialPrice, 
				'quantity' => $quantity, 
				'image' => $image, 
    			'summary' => $summary,
				'description' => $description, 
				'created_date' 	=> time(),
    			'updated_date' 	=> time()
    		);
    		
    		$ingredientId = Ingredient::insert($data);
    		
    		if($ingredientId)
    		{
    			$this->_redirect(BASE_URL .'/adm/ingredient/index?insert=1');
    		}
    		else 
    		{
    			$error = 1;
    		}
    	} 
    	
    	$this->view->error = $error;
    	  	
    	$categoryList = Category::selectCategoryList(array('status' => 1, 'type' => Category::TYPE_CATEGORY_INGREDIENT), 0, 30);
    	$this->view->categoryList = $categoryList;
    }
	
    /**
	*
	*
	* edit ingredient
	*/
	public function editIngredientAction()
    {
    	$request = $this->getRequest();

    	$error = 0;
    	
    	$ingredientId = $this->_getParam('id', 0);

    	if($request->isPost())
    	{
    		$ingredientName = $request->getParam('ingredient_name', '');   		
    		$ingredientName = My_Zend_Globals::strip_word_html($ingredientName,'');
    		
    		$alias = My_Zend_Globals::aliasCreator($ingredientName);
    		
			$image = $request->getParam('ingredient_image', '');   		
    		$image = My_Zend_Globals::strip_word_html($image,'');
			
			$unitPrice = $request->getParam('unit_price','');   		
    		$unitPrice = intval($unitPrice);
			
    		$categoryId = $request->getParam('category_id','');
    		$categoryId = intval($categoryId);
    		
    		$type = $request->getParam('type','');
    		$type = intval($type);
    		
    		$price = $request->getParam('price','');   		
    		$price = floatval(str_replace('.', '', $price));
    	
			$specialPrice = $request->getParam('special_price','');   		
    		$specialPrice = floatval(str_replace('.', '', $specialPrice));
		
			$quantity= $request->getParam('quantity','');   		
    		$quantity = intval($quantity);
			
    		//summary
    		$summary = $request->getParam('summary');
    		$summary = My_Zend_Globals::strip_word_html(trim($summary), '');
    		$summary = htmlspecialchars($summary);
    		
			$description = $request->getParam('description', '');
			
			$pictures = $request->getParam('pic_url');
			$n = count($pictures);
				
			for($i = 0; $i < $n; $i++ )
			{
				$picPro[] = $pictures[$i];
			}
				
			if(empty($image))
			{
				if(!empty($pictures))
				{
					$image = $pictures[0];
				}
			}
			
    		$data = array(	
				'id' => $ingredientId,
				'name' => $ingredientName, 
    			'type' => $type,
    			'alias' => $alias,
    			'category_id' => $categoryId,
				'unit_price' => $unitPrice, 
				'price'	=> $price,
				'special_price' => $specialPrice, 
				'quantity' => $quantity, 
				'image' => $image, 
    			'summary' => $summary,
				'description' => $description, 
    			'updated_date' 	=> time()
    		);
			
    		$rs = Ingredient::update($data);   
    				
    		$this->_redirect(BASE_URL .'/adm/ingredient/index');
    	} 
    	
    	$detail = Ingredient::getItem($ingredientId);
    	
    	if(empty($detail))
    	{
    		$this->_redirect(BASE_URL .'/adm/ingredient/ingredient-list');
    	}
		
    	$picture[] = $detail["image"];
    	$this->view->picture= $picture;
    	
    	$this->view->error = $error;
    	
    	$this->view->detail = $detail;
    	  	
    	$categoryList = Category::selectCategoryList(array('status' => 1, 'type' => Category::TYPE_CATEGORY_INGREDIENT), 0, 30);
    	$this->view->categoryList = $categoryList;
    }
    
    /**
	* 
	* 
	* delete ingredient
	*/
	public function deleteIngredientAction()
    {
    	$ingredientId = $this->_getParam('id', 0);
    	
    	$rs = Ingredient::delete($ingredientId);
    	
    	if($rs)
    	{
    		$error = 0;
    	}
    	else
    	{
    		$error = 1;
    	}
    	
    	$this->_redirect(BASE_URL .'/adm/ingredient/index?act=delete&error='. $error);
    }
}