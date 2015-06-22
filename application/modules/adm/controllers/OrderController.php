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
		$orderId = $this->_getParam('id', 0);
		if($orderId > 0)
		{
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
		
		$user = User::getUser($productOrder['buyer_id']);
		$this->view->user = $user;
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
}