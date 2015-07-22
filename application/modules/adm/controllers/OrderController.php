<?php
class Adm_OrderController extends Zend_Controller_Action
{
	
	public function init() 
	{
		 
	}

	/**
	 * Default action
	 */

	public function indexAction()
	{
		$this->_forward('order-list');
	}
	
	public function orderListAction() 
	{	
		$filter = array();
		$filter['buyer_id'] = 'adm';
		$page = $this->_getParam('page', 1);
		$params = $this->_getAllParams();
		
		if(!isset($params['order_status'])){
			$params['order_status'] = 'all';
		}else{
			$filter['order_status'] = $params['order_status'];
		}
		
		if(isset($params['order_code']) && !empty($params['order_code'])){
			$filter['order_code'] = $params['order_code'];
		}
		
		if(isset($params['from_date']) && !empty($params['from_date'])){
			$filter['from_date'] = ProductOrders::beginDate(strtotime($params['from_date']));
		}
		
		if(isset($params['end_date']) && !empty($params['end_date'])){
			$filter['end_date'] = ProductOrders::endDate(strtotime($params['end_date']));
		}
		
		$limit = 10;
		$offset = ($page - 1) * $limit;

		$listOrders = ProductOrders::getListOrderByUser($filter, $offset, $limit);

		$totalOrder = ProductOrders::getTotalOrder($filter);

		$this->view->listOrders = $listOrders;
		$this->view->params = $params;
		
		$params = array(
			'module' => 'adm',
			'controller' => 'order',
			'action' => 'list'
		);
		
		$this->view->addHelperPath('My/Helper/', 'My_Helper');
		
		$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], 'page', $totalOrder, $page, $limit, PAGE_SIZE, '');
	}
	
	public function detailAction()
	{
		$request = $this->getRequest();
		$orderId = $this->_getParam('id', 0);
		if($orderId > 0)
		{
			if($request->isPost())
			{
				$params = $request->getParams();
				$data_update = array(
					'order_id'		=> intval($params['order_id']),
					'order_status'	=> intval($params['order_status']),
					'order_name'	=> $params['order_name'],
					'order_address'	=> $params['order_address'],
					'order_city'	=> intval($params['order_city']),
					'order_district' => intval($params['order_district']),
					'order_note'	=> $params['order_note'],
					'order_email'	=> $params['order_email'],
					'updated_date'	=> time(),	
				);
				
				ProductOrders::updateProductOrders($data_update);
				$this->_redirect('/adm/order/detail/id/'.$params['order_id']);
			}
			
			$productOrder = ProductOrders::getProductOrder($orderId);
			if(!empty($productOrder))
			{
				$listProduct = ProductOrders::getListProductOfOrder($orderId);
				if(!empty($listProduct))
				{
					foreach($listProduct as &$item)
					{
						switch($item['type'])
						{
							case TYPE_GIFT:
								$coupon = Coupon::getCoupon($item['item_id']);
								$item['name'] = 'Phiếu quà tặng '.My_Zend_Globals::numberFormat($coupon['value']);
								break;
							case TYPE_DISH:
								$dish = Dish::getDish($item['item_id']);
								$item['name'] = $dish['name'];
								break;
							case TYPE_INGREDIENT:
								$ingredient = Ingredient::getItem($item['item_id']);
								$item['name'] = $ingredient['name'];							
								break;
						}
					}
					
					$this->view->listProduct = $listProduct;
					$this->view->productOrder = $productOrder;
				}else
				{
					$this->_redirect('/adm/order');
				}
			}else
			{
				$this->_redirect('/adm/order');
			}
		}else{
			$this->_redirect('/adm/order');
		}		
		
		$listIngredient = Dish::calculateIngredientInDetailOrder($listProduct);
		$this->view->listIngredient = $listIngredient;
	}
	
	public function changeStatusAction()
	{
		$orderId = $this->_getParam('order_id', 0);
		$orderStatus = $this->_getParam('order_status', 0);
		$rs = 0;
		if($orderId > 0){
			$productOrder = ProductOrders::getProductOrder($orderId);
			if(!empty($productOrder)){
				$productOrder['order_status'] = $orderStatus;
				$rs = ProductOrders::updateProductOrders($productOrder);
			}
		}	
		echo $rs; die;
	}
	
	/**
	 * 
	 * group order
	 */
	public function groupOrderAction()
	{
		$ids = $this->_getParam('ids');
		$ids = explode(',', $ids);
		
		$data_return = array();
		
		$orders = ProductOrders::getMultiOrder($ids);
		if (!empty($orders)) {
			foreach ($orders as $order) {
				$listProduct = ProductOrders::getListProductOfOrder($order['order_id']);
				if (!empty($listProduct)) {
					foreach($listProduct as $item) {
						if($item['type'] == TYPE_DISH) {
							$ingredients = Dish::getIngredientInDish($item['item_id']);
							if(!empty($ingredients)) {
								$ingredients = My_Zend_Globals::myArrayFlip($ingredients, 'ingredient_id');
								$multiIngr = Ingredient::getMultiIngredient(array_keys($ingredients));
								$multiIngr = My_Zend_Globals::myArrayFlip($multiIngr, 'id');
					
								foreach($ingredients as $ingr) {
									if(!isset($data_return[$ingr['ingredient_id']])) {
										$data_return[$ingr['ingredient_id']] = array(
											'id'		=> $ingr['ingredient_id'],
											'name'		=> $multiIngr[$ingr['ingredient_id']]['name'],
											'unit'		=> $ingr['unit'],
											'quantity' 	=> $item['quantity']*$ingr['quantity'],
										);
									} else {
										$data_return[$ingr['ingredient_id']] = array(
											'id'		=> $ingr['ingredient_id'],
											'name'		=> $multiIngr[$ingr['ingredient_id']]['name'],
											'unit'		=> $ingr['unit'],
											'quantity' 	=> $data_return[$ingr['ingredient_id']]['quantity'] + ($item['quantity']*$ingr['quantity']),
										);
									}
								}
							}
						} else if($item['type'] == TYPE_INGREDIENT) {
							$ingredient = Ingredient::getItem($item['item_id']);
							if(!isset($data_return[$item['ingredient_id']])) {
								$data_return[$item['ingredient_id']] = array(
									'id'		=> $item['ingredient_id'],
									'name'		=> $ingredient['name'],
									'unit'		=> $ingredient['unit_price'],
									'quantity' 	=> $item['quantity'],
								);
							} else {
								$data_return[$item['ingredient_id']] = array(
									'id'		=> $item['item_id'],
									'name'		=> $$ingredient['name'],
									'unit'		=> $ingredient['unit_price'],
									'quantity' 	=> $data_return[$item['ingredient_id']]['quantity'] + ($item['quantity']),
								);
							}
						}
					}
				}
			}
		}
		
		$this->view->orders = $orders;
		$this->view->listIngredient = $data_return;
		$this->view->ids = implode(',', $ids);
	}
	
	/**
	 * 
	 * print function 
	 */
	public function printFunctionAction()
	{
		$this->_helper->layout->disableLayout();
		$type = $this->_getParam('type', 1);
		
		$ids = $this->_getParam('ids');
		$ids = explode(',', $ids);
		
		$data_return = array();
		
		$orders = ProductOrders::getMultiOrder($ids);
		if (!empty($orders)) {
			foreach ($orders as $order) {
				$listProduct = ProductOrders::getListProductOfOrder($order['order_id']);
				if ($type == 2) {
					foreach($listProduct as &$item)
					{
						switch($item['type'])
						{
							case TYPE_GIFT:
								$coupon = Coupon::getCoupon($item['item_id']);
								$item['name'] = 'Phiếu quà tặng '.My_Zend_Globals::numberFormat($coupon['value']);
								break;
							case TYPE_DISH:
								$dish = Dish::getDish($item['item_id']);
								$item['name'] = $dish['name'];
								break;
							case TYPE_INGREDIENT:
								$ingredient = Ingredient::getItem($item['item_id']);
								$item['name'] = $ingredient['name'];
								break;
						}
					}
				} else {
					if (!empty($listProduct)) {
						foreach($listProduct as $item) {
							if($item['type'] == TYPE_DISH) {
								$ingredients = Dish::getIngredientInDish($item['item_id']);
								if(!empty($ingredients)) {
									$ingredients = My_Zend_Globals::myArrayFlip($ingredients, 'ingredient_id');
									$multiIngr = Ingredient::getMultiIngredient(array_keys($ingredients));
									$multiIngr = My_Zend_Globals::myArrayFlip($multiIngr, 'id');
										
									foreach($ingredients as $ingr) {
										if(!isset($data_return[$ingr['ingredient_id']])) {
											$data_return[$ingr['ingredient_id']] = array(
													'id'		=> $ingr['ingredient_id'],
													'name'		=> $multiIngr[$ingr['ingredient_id']]['name'],
													'unit'		=> $ingr['unit'],
													'quantity' 	=> $item['quantity']*$ingr['quantity'],
											);
										} else {
											$data_return[$ingr['ingredient_id']] = array(
													'id'		=> $ingr['ingredient_id'],
													'name'		=> $multiIngr[$ingr['ingredient_id']]['name'],
													'unit'		=> $ingr['unit'],
													'quantity' 	=> $data_return[$ingr['ingredient_id']]['quantity'] + ($item['quantity']*$ingr['quantity']),
											);
										}
									}
								}
							} else if($item['type'] == TYPE_INGREDIENT) {
								$ingredient = Ingredient::getItem($item['item_id']);
								if(!isset($data_return[$item['ingredient_id']])) {
									$data_return[$item['ingredient_id']] = array(
											'id'		=> $item['ingredient_id'],
											'name'		=> $ingredient['name'],
											'unit'		=> $ingredient['unit_price'],
											'quantity' 	=> $item['quantity'],
									);
								} else {
									$data_return[$item['ingredient_id']] = array(
											'id'		=> $item['item_id'],
											'name'		=> $$ingredient['name'],
											'unit'		=> $ingredient['unit_price'],
											'quantity' 	=> $data_return[$item['ingredient_id']]['quantity'] + ($item['quantity']),
									);
								}
							}
						}
					}
				}
			}
		}
		
		$this->view->orders = array_values($orders);
		$this->view->listIngredient = $data_return;
		$this->view->listProduct = $listProduct;
		$this->render('print-type-'.$type);
	}
}