<?php
class Adm_AttributeController extends Zend_Controller_Action
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
		$this->_forward('attribute-list');
	}
	
	/**
	 * list banner
	 * 
	 */
	public function attributeListAction()
	{
		Role::isAllowed(Permission::ATTRIBUTE_LIST, true);
		
		$params = $this->_getAllParams();
		
		$limit = 10;
		
		$page = isset($params['page']) ? intval($params['page']) : 1;
		$offset = ($page - 1) * $limit;
		
		$params['status'] = 'all';
		$params['category_id'] = 0;
		
		$attributes = Attribute::getList($params, $offset, $limit);
		$total  = Attribute::getTotal($params);
		
		if(!empty($attributes)){
    		foreach($attributes as &$attr)
    		{		
    			if(!empty($attr['category_id']))
    			{   				
    				$category = explode(",", $attr['category_id']);	
    				$cateName = "";
    				foreach ($category as $categoryId)
    				{   					
    					$cateDetail = Dish::$_ARRAY_TYPE[$categoryId];	
     					$cateName.= $cateDetail['name']."(".$categoryId.") , ";   				
    				}
    				$cateName = substr($cateName, 0, strlen($cateName) - 2);
    				$attr['category_name'] = $cateName;    			
    			}
    		}
    	}
		
		$filters = $params;
		unset($filters['controller']);
		unset($filters['module']);
		unset($filters['action']);
		
		$this->view->addHelperPath('My/Helper/', 'My_Helper');
		$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], $filters, $total, $page, $limit, PAGE_SIZE, "");
		
		$this->view->params = $params;
		$this->view->attributes = $attributes;
	}
		
	/**
	 *
	 * edit photo action
	 */
	public function editAttributeAction()
	{
		Role::isAllowed(Permission::ATTRIBUTE_EDIT, true);
	
		$attributeId = $this->_getParam('id', 0);
		$attributeId = intval($attributeId);
		
		$attribute = Attribute::getAttribute($attributeId);
		$attributeCatList = explode(",", $attribute['category_id']);
		$request = $this->getRequest();
	
		if(empty($attribute))
		{
			$this->_redirect(BASE_URL .'/adm/attribute/attribute-list');
		}
		
		if($request->isPost())
		{
			$attributeName = $request->getParam('attribute_name', '');      		  	
    		$isVisible = $request->getParam('is_visible', 0);
    		
    		$inputType = $request->getParam('input_type', '');
    		$isRequire = $request->getParam('is_require', 0);    		
    		$options = $request->getParam('option');
    				
    		$attributeName = My_Zend_Globals::strip_word_html($attributeName,'');
    		
    		$category = "";
    		$categoryList = $request->getParam('input_cate', '');
    		$totalCate = count($categoryList);
    		if(!empty($categoryList))
    		{
	    		for($i=0;$i < $totalCate; $i++)
	    		{
	    			$category.=$categoryList[$i];
	    			if($i != ($totalCate - 1))
	    				$category.=","; 		
	    		}  
    		}
    		
    		$data = array(
    			'attribute_id'		=> $attributeId,
    			'attribute_name'	=> trim($attributeName),    				    				
    			'is_visible'		=> $isVisible,
    			'is_require'		=> $isRequire,
    		    'category_id'		=> $category,    						
    			'input_type'		=> $inputType,    					
    			'updated_date'		=> time()
    		);
    		
    		$rs = Attribute::update($data);
   
    		if($rs)
    		{
    			//update attribute - category
    			Attribute::deleteAttributeCategory($attributeId);
    			for($i=0; $i < $totalCate; $i++)
	    		{
	    			$dataInsert = array(
	    				'category_id' => $categoryList[$i],
	    				'attribute_id' => $attributeId
	    			);
					
	    			Attribute::insertAttributeCategory($dataInsert);
	    		}
    			
    			switch($inputType)
    			{    				
    				case Attribute::TYPE_DROPDOWN:
    				case Attribute::TYPE_MULTIPLE:
    					$deletedOptions = explode(',', $options['delete_attributes']);   					
    					if(!empty($options['value']))
    					{
    						$totalOption = count($options['option_id']);
    						
    						foreach ($options['value'] as $key => $value)
    						{
    							$optionId = isset($options['option_id'][$key]) ? intval($options['option_id'][$key]) : 0;
    							
    							if($optionId > 0 && !in_array($optionId, $deletedOptions))
    							{
    								$option = array(
    									'option_id'			=> $optionId,
    									'attribute_id'		=> $attributeId,
    									'value'				=> $value,			
    									'sort_order'		=> intval($options['position'][$key])
    								);
    								
    								Attribute::updateAttributeOption($option);    								    								
    							}
    							elseif($optionId == 0 && !empty($value)) // insert new record 
    							{    								
    								$option = array(    									
    									'attribute_id'		=> $attributeId,
    									'value'				=> $value,				
    									'sort_order'		=> intval($options['position'][$key])
    								);
    								
    								$tmp = $key + 1 - $totalOption;
    								
    								$optionId = Attribute::insertAttributeOption($option);   								
    							}		
    						}
    					}

    					if(!empty($deletedOptions))
    					{
    						foreach ($deletedOptions as $optionId)
    						{
    							Attribute::deleteAttributeOption($optionId);
    						}
    					}
    					    					
    					break;
    			}
    			
    			$this->_redirect(BASE_URL .'/adm/attribute');
    		}
    		else 
    		{
    			$error = 1;
    		}
		}
		
		$category = Category::selectCategoryList(array('status' => 1), 0, 100);
		$this->view->category = $category;
		$this->view->attribute = $attribute;
		
		$options = array();
    	
    	$inputType = $attribute['input_type'];
    	
    	switch ($inputType)
    	{
    		case Attribute::TYPE_DROPDOWN:
    		case Attribute::TYPE_MULTIPLE:
    			$options = Attribute::getAttributeOption($attributeId);  			
    			break;
    	}
    	
    	$this->view->options = $options;
    	$this->view->attributeCatList = $attributeCatList;
	}
	
	/**
	 *
	 * add photo action
	 */
	public function addAttributeAction()
	{
		Role::isAllowed(Permission::ATTRIBUTE_ADD, true);
	
		$request = $this->getRequest();
		
		if ($this->getRequest()->isPost())
		{			
			$attributeName = $request->getParam('attribute_name', '');
			$attributeName = My_Zend_Globals::strip_word_html($attributeName,'');
			
    		$inputType = $request->getParam('input_type', '');
    		
    		$isVisible = $request->getParam('is_visible', 0);
    		
    		$isRequire = $request->getParam('is_require', 0);
    		
    		$options = $request->getParam('option');
    		
    		$category = "";
			$categoryList = $request->getParam('input_cate', '');
    		$totalCate = count($categoryList);
    		
    		if(!empty($categoryList))
    		{
	    		for($i=0; $i < $totalCate; $i++)
	    		{
	    			$category.= $categoryList[$i];
	    			if($i != ($totalCate - 1))
	    				$category.=","; 		
	    		}
    		}
    		
    		$data = array(    					
    			'attribute_name'	=> trim($attributeName),
    			'input_type'		=> $inputType,    	
    			'category_id'		=> $category,    						
    			'is_visible'		=> $isVisible,
    			'is_require'		=> $isRequire,
    			'updated_date'		=> time()					
    		);
    		
    		$attributeId = Attribute::insert($data);
    		
    		if($attributeId)
    		{
    			//insert attribute - category
	    		for($i=0; $i < $totalCate; $i++)
	    		{
	    			$dataInsert = array(
	    				'category_id' => $categoryList[$i],
	    				'attribute_id' => $attributeId
	    			);
					
	    			Attribute::insertAttributeCategory($dataInsert);
	    		}
    			
    			switch($inputType)
    			{
    				case Attribute::TYPE_DROPDOWN:   
    				case Attribute::TYPE_MULTIPLE: 					    					    					
    					if(!empty($options['value']))
    					{    						
    						foreach ($options['value'] as $key => $value)
    						{
    							if(!empty($value))
    							{
    								$option = array(
    									'attribute_id'	  => $attributeId,
    									'value'			  => $value,
    									'sort_order'	  => intval($options['position'][$key])
    								);
    								
    								$optionId = Attribute::insertAttributeOption($option); 					
    							}
    						}
    					}
    					break;
    			}
    			
    			$this->_redirect(BASE_URL .'/adm/attribute/index/');
    		}
    		else 
    		{
    			$error = 1;
    		}
		}
		
		$category = Category::selectCategoryList(array('status' => 1), 0, 100);
		$this->view->category = $category;
	}
	
	/**
	 *
	 * delete attribute
	 */
	public function deleteAttributeAction()
	{
		Role::isAllowed(Permission::ATTRIBUTE_DELETE,true);
	
		$id = $this->_getParam('id', 0);
		$id = intval($id);
			
		$attribute = Attribute::getAttribute($id);
		
		if(empty($attribute))
		{
			echo 0; exit;
		}
		
		$rs = Attribute::delete($id);		
		$rs = Attribute::deleteAttributeCategory($id);
		$rs = Attribute::deleteAttributeOptionByAttributeId($id);
		
		echo 1; exit;
	}
	
	/**
	 *
	 * update status attribute action
	 */
	public function updateStatusAttributeAction() 
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$attributeId = intval($this->_getParam('id', 0));
		$flag = 0;

		$status = $this->_getParam('status', 0);
		$status = intval($status);
		
		if ($attributeId > 0) 
		{
			$data['attribute_id'] = $attributeId;
			$data['is_visible'] = $status;
			$data['updated_date'] = time();
			$flag = Attribute::update($data);
		}
		
		if($flag)
			$this->_redirect(BASE_URL.'/adm/attribute?result=true');
		else
			$this->_redirect(BASE_URL.'/adm/attribute?result=false');
	}
}