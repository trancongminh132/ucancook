<?php
class Adm_DishController extends Zend_Controller_Action
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
		$this->_forward('dish-list');
	}
	
    /**
	*
	*	dish list action
	*
	*/
    public function dishListAction()
    {    	
    	$page = $this->_getParam('page', 1);   	
    	$params = $this->_getAllParams();
    	
		$limit = $this->_getParam('limit', 30);    		
    	$limit = intval($limit);
			
    	$offset = ($page - 1) * $limit;
    	
		$dishName = $this->_getParam('dish_name', '');   		
    	$dishName = My_Zend_Globals::strip_word_html($dishName,'');
		
		$type = $this->_getParam('type', '');    		
    	$type = intval($type);
		
		$chefId = $this->_getParam('chef_id', '');    		
    	$chefId = intval($chefId);
		
		$filters = array(
			'type' => $type,
			'chef_id' => $chefId,
			'dish_name' => $dishName,
			'limit' => $limit
		);
		
    	$total = Dish::getTotalDish($filters);    	
    	$dishes = Dish::getListDish($filters, $offset, $limit);
    	
    	if(!empty($dishes))
    	{
    		foreach($dishes as &$item)
    		{
    			$chef = Chef::getChef($item['chef_id']);
    			$item['chef_name'] = $chef['chef_name'];
    		}
    	}
    	
    	$this->view->addHelperPath('My/Helper/', 'My_Helper');
    	$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], $filters, $total, $page, $limit, PAGE_SIZE, "");
    	
        $this->view->dishes = $dishes;
		$this->view->filters = $filters;
		
		$chefs = Chef::getList(array(), 0,100);
		$this->view->chefs = $chefs;
		
    	My_Zend_Globals::setTitle('Quản lý món ăn');
    }
	
	/**
	*
	* add dish action
	*
	*/ 
	public function addDishAction()
    {
    	$request = $this->getRequest();
    	$error = 0;   	
    	
    	$type = $this->_getParam('type', 0);
    	$type = intval($type);
    	
    	if(!empty($type))
    	{
    		// get list attribute in category
    		$attributes = Attribute::getListAttributeByCategory($type);
    		$this->view->attributes = $attributes;
    	}
    	
    	if($request->isPost())
    	{    
    		$filePdf = "";
    		if ($_FILES['file_pdf']['size'])
    		{
    			$file = $_FILES['file_pdf'];
    		
    			$upload = new Upload();
    			$destFolder = array('file', date('Y'));
    			$rsUpload = $upload->uploadFile($file, $destFolder);
    			$filePdf = $rsUpload['url'];
    		}
    		
    		$dishName = $request->getParam('dish_name', '');   		
    		$dishName = My_Zend_Globals::strip_word_html($dishName,'');
    		
    		$alias = My_Zend_Globals::aliasCreator($dishName);
    		
			$type= $request->getParam('type','');   		
    		$type = intval($type);
			
    		$num_dish= $request->getParam('num_dish','');
    		$num_dish = intval($num_dish);
    		
    		$status = $this->_getParam('status', 1);
    		$status = intval($status);
    		
    		$chefId= $request->getParam('chef_id','');
    		$chefId = intval($chefId);
    		
    		$price= $request->getParam('price','');   		
    		$price = floatval(str_replace('.', '', $price));
    		
			$specialPrice= $request->getParam('special_price','');   		
    		$specialPrice = floatval(str_replace('.', '', $specialPrice));
			
			$ingredients= $request->getParam('ingredients', array());   		
			$quantity_ingr 	= $request->getParam('quantity_ingr');
			$unit_ingr		= $request->getParam('unit_ingr');
			
			$image = $request->getParam('dish_image', '');   		
    		$image = My_Zend_Globals::strip_word_html($image,'');
			
    		$pictures = $request->getParam('pic_url');
    		$n = count($pictures);
    		
    		for($i = 0; $i < $n; $i++ )
    		{
    			$picPro[] = $pictures[$i];
    		}
    		// neu user chua chon hinh thi lay hinh dau tien trong ds upload
    		if(empty($image))
    		{
    			if(!empty($pictures))
    			{
    				$image = $pictures[0];
    			}
    		}
    		
			$description = $request->getParam('description', '');   		
    		
			// get category info
			$attributeDataValue = $request->getParam('attribute');
				
			require_once APPLICATION_PATH .'/configs/attributes.php';
			global $_INPUT_TYPES;
			
			$arrayAttribute = array();
				
			if(!empty($attributes) && is_array($attributes))
			{
				foreach ($attributes as $attribute)
				{
					$arrayAtt = array();
					if(isset($_INPUT_TYPES[$attribute['input_type']]))
					{
						switch ($attribute['input_type'])
						{
							case 'dropdown':
								$value = isset($attributeDataValue[$attribute['attribute_id']]) ? $attributeDataValue[$attribute['attribute_id']] : $attribute['default_value'];
								$arrayAtt[$attribute['attribute_id']] = array(
										'id'    => $attribute['attribute_id'],
										'value' => intval($value));
								break;
							case 'text':
								$value = isset($attributeDataValue[$attribute['attribute_id']]) ? My_Zend_Globals::strip_word_html(trim($attributeDataValue[$attribute['attribute_id']]), '') : $attribute['default_value'];
								$arrayAtt[$attribute['attribute_id']] = array(
										'id'    => $attribute['attribute_id'],
										'value' => My_Zend_Globals::strip_word_html(trim($value), ''));
								break;
							case 'varchar':
								$value = isset($attributeDataValue[$attribute['attribute_id']]) ? My_Zend_Globals::strip_word_html(trim($attributeDataValue[$attribute['attribute_id']]), '') : $attribute['default_value'];
								$arrayAtt[$attribute['attribute_id']] = array(
										'id'    => $attribute['attribute_id'],
										'value' => My_Zend_Globals::strip_word_html(trim($value), ''));
								break;
							case 'checkbox':
								$value = isset($attributeDataValue[$attribute['attribute_id']]) ? $attributeDataValue[$attribute['attribute_id']] : $attribute['default_value'];
								$arrayAtt[$attribute['attribute_id']] = array(
										'id'    => $attribute['attribute_id'],
										'value' => intval($value));
								break;
							case 'multiple':
								$i = 0;
								$valueId = array();
								foreach($attributeDataValue[$attribute['attribute_id']] as $multiple)
								{
									$valueId[] = $multiple;
								}
			
								$arrayAtt[$attribute['attribute_id']] = array(
										'id'    => $attribute['attribute_id'],
										'value' => $valueId);
									
								break;
						}
					}
			
					$arrayAttribute[$attribute['attribute_id']] = $arrayAtt;
				}
			}
			
			$strIngredient = "";
			$totalIngr = count($ingredients);
				
			if(!empty($ingredients))
			{
				for($i=0; $i < $totalIngr; $i++)
				{
					$strIngredient.= $ingredients[$i];
					if($i != ($totalIngr - 1))
						$strIngredient.=",";
				}
			}
			
    		$data = array(	
				'name' => $dishName, 
    			'alias' => $alias,
				'type' => $type, 
    			'num_dish' => $num_dish,
    			'chef_id' => $chefId,
				'price'	=> $price,
				'special_price' => $specialPrice, 							
				'attributes' => Zend_Json::encode($arrayAttribute), 
				'image' => $image, 
    			'multi_image'	=> Zend_Json::encode($picPro),
				'description' => $description, 
    			'status' => $status,
    			'ingredient' => $strIngredient,
				'created_date' 	=> time(),
    			'updated_date' 	=> time(),
    			'file_pdf'		=> $filePdf,
    		);
    		
    		$dishId = Dish::insert($data);
    		
    		if($dishId)
    		{
    			//insert dish - ingredient
    			for($i=0; $i < $totalIngr; $i++)
    			{
    				$dataInsert = array(
    					'ingredient_id' => $ingredients[$i],
    					'dish_id' 	=> $dishId,
    					'quantity'	=> $quantity_ingr[$i],
    					'unit'		=> $unit_ingr[$i],
	    			);
					
    				Dish::insertDishIngredient($dataInsert);
    			}
    			
    			$this->_redirect(BASE_URL .'/adm/dish/index/?insert=1');
    		}
    		else 
    		{
    			$error = 1;
    		}
    	} 
    	
    	$this->view->error = $error;
    	
    	$chefs = Chef::getList(array(), 0,100);
    	$this->view->chefs = $chefs;
    	
    	$ingredients = Ingredient::getListItem(array(), 0, 1000);
    	$this->view->ingredients = $ingredients;
    	
    	$this->view->type = $type;
    }
	
    /**
	*
	*
	* edit dish
	*/
	public function editDishAction()
    {
    	$request = $this->getRequest();

    	$error = 0;
    	
    	$dishId = $this->_getParam('id', 0);
		$type = $this->_getParam('type');
    	
		$dish = Dish::getDish($dishId);
		 
		if(empty($dish))
		{
			$this->_redirect(BASE_URL .'/adm/dish/dish-list');
		}
		
		if(empty($type))
			$type = $dish['type'];
		 
		if(!empty($type))
		{
			// get list attribute in category
			$attributes = Attribute::getListAttributeByCategory($type);
			$this->view->attributes = $attributes;
		}
		
    	if($request->isPost())
    	{
    		$filePdf = $request->getParam('filePdf', '');
    		$filePdf = My_Zend_Globals::strip_word_html($filePdf,'');
    		
    		if ($_FILES['file_pdf']['size'])
    		{
    			$file = $_FILES['file_pdf'];
				
    			$upload = new Upload();
	    		$destFolder = array('file', date('Y'));
	    		$rsUpload = $upload->uploadFile($file, $destFolder);
	    		$filePdf = $rsUpload['url'];
    		}
    		
    		$dishName = $request->getParam('dish_name', '');   		
    		$dishName = My_Zend_Globals::strip_word_html($dishName,'');
    		
    		$alias = My_Zend_Globals::aliasCreator($dishName);
    		
			$type= $request->getParam('type','');   		
    		$type = intval($type);
			
    		$num_dish= $request->getParam('num_dish','');
    		$num_dish = intval($num_dish);
    		
    		$status = $this->_getParam('status', 1);
    		$status = intval($status);
    		
    		$chefId= $request->getParam('chef_id','');
    		$chefId = intval($chefId);
    		
    		$price= $request->getParam('price','');   		
    		$price = floatval(str_replace('.', '', $price));
    		
			$specialPrice= $request->getParam('special_price','');   		
    		$specialPrice = floatval(str_replace('.', '', $specialPrice));
			
			$ingredients	= $request->getParam('ingredients', array());   		
    		$quantity_ingr 	= $request->getParam('quantity_ingr');
    		$unit_ingr		= $request->getParam('unit_ingr');
			
			$image = $request->getParam('dish_image', '');   		
    		$image = My_Zend_Globals::strip_word_html($image,'');
			
    		$pictures = $request->getParam('pic_url');
    		$n = count($pictures);
    		
    		for($i = 0; $i < $n; $i++ )
    		{
    			$picPro[] = $pictures[$i];
    		}
    		// neu user chua chon hinh thi lay hinh dau tien trong ds upload
    		if(empty($image))
    		{
    			if(!empty($pictures))
    			{
    				$image = $pictures[0];
    			}
    		}
    		
			$description = $request->getParam('description', '');   		
    		
			// get category info
			$attributeDataValue = $request->getParam('attribute');
				
			require_once APPLICATION_PATH .'/configs/attributes.php';
			global $_INPUT_TYPES;
			
			$arrayAttribute = array();
				
			if(!empty($attributes) && is_array($attributes))
			{
				foreach ($attributes as $attribute)
				{
					$arrayAtt = array();
					if(isset($_INPUT_TYPES[$attribute['input_type']]))
					{
						switch ($attribute['input_type'])
						{
							case 'dropdown':
								$value = isset($attributeDataValue[$attribute['attribute_id']]) ? $attributeDataValue[$attribute['attribute_id']] : $attribute['default_value'];
								$arrayAtt[$attribute['attribute_id']] = array(
										'id'    => $attribute['attribute_id'],
										'value' => intval($value));
								break;
							case 'text':
								$value = isset($attributeDataValue[$attribute['attribute_id']]) ? My_Zend_Globals::strip_word_html(trim($attributeDataValue[$attribute['attribute_id']]), '') : $attribute['default_value'];
								$arrayAtt[$attribute['attribute_id']] = array(
										'id'    => $attribute['attribute_id'],
										'value' => My_Zend_Globals::strip_word_html(trim($value), ''));
								break;
							case 'varchar':
								$value = isset($attributeDataValue[$attribute['attribute_id']]) ? My_Zend_Globals::strip_word_html(trim($attributeDataValue[$attribute['attribute_id']]), '') : $attribute['default_value'];
								$arrayAtt[$attribute['attribute_id']] = array(
										'id'    => $attribute['attribute_id'],
										'value' => My_Zend_Globals::strip_word_html(trim($value), ''));
								break;
							case 'checkbox':
								$value = isset($attributeDataValue[$attribute['attribute_id']]) ? $attributeDataValue[$attribute['attribute_id']] : $attribute['default_value'];
								$arrayAtt[$attribute['attribute_id']] = array(
										'id'    => $attribute['attribute_id'],
										'value' => intval($value));
								break;
							case 'multiple':
								$i = 0;
								$valueId = array();
								foreach($attributeDataValue[$attribute['attribute_id']] as $multiple)
								{
									$valueId[] = $multiple;
								}
			
								$arrayAtt[$attribute['attribute_id']] = array(
										'id'    => $attribute['attribute_id'],
										'value' => $valueId);
									
								break;
						}
					}
			
					$arrayAttribute[$attribute['attribute_id']] = $arrayAtt;
				}
			}		
    		
    		$strIngredient = "";
			$totalIngr = count($ingredients);
				
			if(!empty($ingredients))
			{
				for($i=0; $i < $totalIngr; $i++)
				{
					$strIngredient.= $ingredients[$i];
					if($i != ($totalIngr - 1))
						$strIngredient.=",";
				}
			}
			
    		$data = array(	
				'id' => $dishId,
				'name' => $dishName,
    			'alias' => $alias,
    			'num_dish' => $num_dish,
				'type' => $type, 
    			'chef_id' => $chefId,
				'price'	=> $price,
				'special_price' => $specialPrice, 							
				'attributes' => Zend_Json::encode($arrayAttribute), 
				'image' => $image, 
    			'multi_image'	=> Zend_Json::encode($picPro),
				'description' => $description, 
    			'status' => $status,
    			'ingredient' => $strIngredient,
    			'updated_date' 	=> time(),
    			'file_pdf'		=> $filePdf,
    		);
			
    		$rs = Dish::update($data); 

    		if($rs)
    		{
    			//update dish - ingredient
    			Dish::deleteDishIngredient($dishId);
    			
    			for($i=0; $i < $totalIngr; $i++)
    			{
    				$dataInsert = array(
    					'ingredient_id' => intval($ingredients[$i]),
    					'dish_id' 	=> intval($dishId),
    					'quantity'	=> $quantity_ingr[$i],
    					'unit'		=> intval($unit_ingr[$i]),	
	    			);
    				
    				Dish::insertDishIngredient($dataInsert);
    			}
    		}
    		
    		$this->_redirect(BASE_URL .'/adm/dish');

    	} 
    	
    	$this->view->error = $error;    	
    	$this->view->dish = $dish;   	  	
    	$this->view->type = $type;
    	//get list picture
    	$picture = Zend_Json::decode($dish["multi_image"]);
    	$this->view->picture= $picture;
    	
    	//get list value user da chon
    	$value = Zend_Json::decode($dish["attributes"]);
    	$this->view->value = $value;
    	
    	$chefs = Chef::getList(array(), 0,100);
    	$this->view->chefs = $chefs;
    	 
    	$ingredients = Ingredient::getListItem(array(), 0, 1000);
    	$this->view->ingredients = $ingredients;
    	
    	$ingDishList = Dish::getIngredientInDish($dish['id']);
    	$this->view->ingDishList = $ingDishList;
    }
    
    /**
	* 
	* 
	* delete dish
	*/
	public function deleteDishAction()
    {
    	$dishId = $this->_getParam('id', 0);
    	
    	$dish = Dish::getDish($dishId);
    	
    	if(empty($dish))
    		return false;
    	
    	$rs = Dish::delete($dishId);
    	
    	if($rs)
    	{
    		$error = 0;
    	}
    	else
    	{
    		$error = 1;
    	}
    	
    	$this->_redirect(BASE_URL .'/adm/dish/index?act=delete&error='. $error);
    }
    
    /**
     *
     * update status dish action
     */
    public function updateStatusDishAction()
    {
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    
    	$dishId = intval($this->_getParam('id', 0));
    	$flag = 0;
    
    	$dish = Dish::getDish($dishId);
    	
    	if(empty($dish))
    		return false;
    	
    	$status = $this->_getParam('status', 0);
    	$status = intval($status);
    
    	if ($dishId > 0)
    	{
    		$data['id'] = $dishId;
    		$data['status'] = $status;
    		$data['updated_date'] = time();
    		$flag = Dish::update($data);
    	}
    
    	if($flag)
    		$this->_redirect(BASE_URL.'/adm/dish?result=true');
    	else
    		$this->_redirect(BASE_URL.'/adm/dish?result=false');
    }
    
    /**
     *
     * add new row ingredient
     *
     */
    public function addNewIngredientAction()
    {
    	$ingredients = Ingredient::getListItem(array(), 0, 1000);
    	
    	$html = '<tr><td><select style="width:127px" class="cb_ingredient" name="ingredients[]">';
    	$html .='<option value="0">Chọn nguyên liệu</option>';
    	foreach($ingredients as $item) {
			$html .='<option data-unit="'.$item['unit_price'].'" value="'.$item['id'].'">'.My_Zend_Globals::cutString($item['name'], 0, 40).'</option>';
		}
		$html .='</select></td><td><input type="text" name="quantity_ingr[]"></td><td><select class="cb_unit_ingr" name="unit_ingr[]">';
		foreach (Dish::$_ARRAY_TYPE_UNIT as $key => $unit) {
			$html .='<option value="'.$key.'">'.$unit.'</option>';
		}
		$html .='</select></td></tr>';
    	
    	$array_return = array(
    		'html' => $html,
    	);
    	echo json_encode($array_return); exit;
    }
}