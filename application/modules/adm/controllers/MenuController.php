<?php
class Adm_MenuController extends Zend_Controller_Action
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
		$this->_forward('menu-list');
	}
	
    /**
	*
	*	menu list action
	*
	*/
    public function menuListAction()
    {    	
    	$page = $this->_getParam('page', 1);   	
    	$limit = $this->_getParam('limit', 30);    	    	
    	$offset = ($page - 1) * $limit;
    	    	$saleDate = $this->_getParam('sale_date');    	    	if(!empty($saleDate))    		$saleDate = strtotime($saleDate);    	
		$dishId = $this->_getParam('dish_id', '');    		
    	$dishId = intval($dishId);
		
		$filters = array(
			'dish_id' => $dishId,			'limit'   => $limit,			'sale_date' => $saleDate
		);
		
    	$total = Menu::getTotalMenu($filters);    	
    	$menus = Menu::getListMenu($filters, $offset, $limit);
    	    	$arrayDishIds = array();    	    	if(!empty($menus))    	{    		foreach($menus as $item)    		{    			$arrayDishIds[] = $item['dish_id'];    		}    		$arrayDishIds = array_unique($arrayDishIds);    	}    	    	$multiDish = Dish::getListDish(array('status' => 1, 'dish_id' => $arrayDishIds));    	$multiDish = My_Zend_Globals::myArrayFlip($multiDish, 'id');    	$this->view->multiDish = $multiDish;    	
    	$this->view->addHelperPath('My/Helper/', 'My_Helper');
        $this->view->paging = $this->view->pagingadmin('adm','menu','menu-list', array(), $total, $page, $limit, PAGE_SIZE, '');
       	
        $this->view->menues = $menus;
		$this->view->filters = $filters;
				$dishes = Dish::getListDish(array('status' => 1), 0, 1000);		$this->view->dishes = $dishes;
    }
	
	/**
	*
	* add menu action
	*
	*/ 
	public function addMenuAction()
    {
    	$request = $this->getRequest();
    	$error = 0;   	    	
    	if($request->isPost())
    	{
			$dishId = $request->getParam('dish_id','');   		
    		$dishId = intval($dishId);
			    		$quantity = $request->getParam('quantity','');    		$quantity = floatval(str_replace('.', '', $quantity));    		
			$pattern = $request->getParam('recurrence_pattern','');   				$pattern = My_Zend_Globals::strip_word_html($pattern, '');						$days = $request->getParam('days','');						$start_date = $request->getParam('start_date');   	
    		$start_date = ProductOrders::beginDate($start_date);    		    		$end_date = $request->getParam('end_date');    		$end_date = ProductOrders::endDate($end_date);    		
			$quantity= $request->getParam('quantity','');   		    		$quantity = floatval(str_replace('.', '', $quantity));    		    		//type daily    		if ($pattern == "daily") {    			for($i = $start_date; $i <= $end_date; $i+=86400)     			{    				$data = array(    					'dish_id' => $dishId,    					'sale_date' => $i,    					'quantity'	=> $quantity,    					'created_date' 	=> time(),    				);    				    				$menuId = Menu::insert($data);    			}    		} else if ($pattern == "weekly") {    			for($i = $start_date; $i <= $end_date; $i+=86400)    			{    				$data = array(    						'dish_id' => $dishId,    						'sale_date' => $i,    						'quantity'	=> $quantity,    						'created_date' 	=> time(),    				);    			    				$menuId = Menu::insert($data);    			}    		}    		echo"done";die;
    		
    		if($menuId)
    		{
    			$this->_redirect(BASE_URL .'/adm/menu/add-menu?result=success');
    		}
    		else 
    		{
    			$error = 1;
    		}
    	} 
		
    	$dishes = Dish::getListDish(array('status' => 1), 0, 1000);    	$this->view->dishes = $dishes;
    }
	
    /**
	*
	*
	* edit menu
	*/
	public function editMenuAction()
    {
    	$request = $this->getRequest();

    	$error = 0;
    	
    	$menuId = $this->_getParam('id', 0);

    	if($request->isPost())
    	{
    		$dishId= $request->getParam('dish_id','');   		    		$dishId = intval($dishId);						$saleDate= $request->getParam('sale_date','');   		    		$saleDate = strtotime($saleDate);    		$saleDate = ProductOrders::beginDate($saleDate);    					$quantity= $request->getParam('quantity','');   		    		$quantity = floatval(str_replace('.', '', $quantity));   		
    		
    		$data = array(	
				'id' => $menuId,
				'dish_id' => $dishId, 				'sale_date' => $saleDate, 				'quantity'	=> $quantity,     			'sale_date' 	=> $saleDate
    		);
			
    		$rs = Menu::update($data);   		
    		$this->_redirect(BASE_URL .'/adm/menu/edit-menu?id='.$menuId.'&result=success');

    	} 
    	
    	$menu = Menu::getItem($menuId);
    	
    	if(empty($menu))
    	{
    		$this->_redirect(BASE_URL .'/adm/menu/menu-list');
    	}
		
    	$this->view->menu = $menu;     	  	      	$dishes = Dish::getListDish(array('status' => 1), 0, 1000);    	$this->view->dishes = $dishes;
    }
    
    /**
	* 
	* 
	* delete menu
	*/
	public function deleteMenuAction()
    {
    	$menuId = $this->_getParam('id', 0);
    	    	$menu = Menu::getItem($menuId);    	    	$error = 0;    	$arrayReturn = array(    		'error' => $error    	);    	    	if(empty($menu))    	{    		echo json_encode($arrayReturn);exit;    	}    	
    	$rs = Menu::delete($menuId);
    	
    	if($rs)
    	{
    		$error = 0;
    	}
    	else
    	{
    		$error = 1;
    	}
    	    	$arrayReturn = array(    		'error' => $error
    	);    	    	echo json_encode($arrayReturn);exit;
    }
}