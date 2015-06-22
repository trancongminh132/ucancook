<?php
class Adm_CouponController extends Zend_Controller_Action
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
		$this->_forward('coupon-list');
	}
	
	/**
	 * list coupon
	 * 
	 */
	public function couponListAction()
	{
		$params = $this->_getAllParams();
		
		$limit = 10;
		
		$page = isset($params['page']) ? intval($params['page']) : 1;
		$offset = ($page - 1) * $limit;
		
		$coupons = Coupon::getList($params, $offset, $limit);
		$total  = Coupon::countTotal($params);
		
		$filters = $params;
		unset($filters['controller']);
		unset($filters['module']);
		unset($filters['action']);
		
		$this->view->addHelperPath('My/Helper/', 'My_Helper');
		$this->view->paging = $this->view->pagingadmin($params['module'], $params['controller'], $params['action'], $filters, $total, $page, $limit, PAGE_SIZE, "");
		
		$this->view->params = $params;
		$this->view->coupons = $coupons;
	}
		
	/**
	 *
	 * edit coupon action
	 */
	public function editCouponAction()
	{
		$id = $this->_getParam('id', 0);
		$id = intval($id);
		
		$coupon = Coupon::getCoupon($id);
		$request = $this->getRequest();
	
		if(empty($coupon))
		{
			$this->_redirect(BASE_URL .'/adm/coupon/coupon-list');
		}
		
		if($request->isPost())
		{
			$couponName= $request->getParam('coupon_name');
			$couponName = My_Zend_Globals::strip_word_html(trim($couponName), '');
			$couponName = htmlspecialchars($couponName);
			
			$status = intval($this->_getParam('status'));
			
			$price= $request->getParam('price');
			$price = floatval(str_replace('.', '', $price));
			
			$value= $request->getParam('value');
			$value = floatval(str_replace('.', '', $value));
			
			if(!empty($couponName) && !empty($price))
			{
				$data = array(
					'id'	=> $id,
					'coupon_name'	=> $couponName,
					'status'		=> $status,
					'price'			=> $price,
					'value'			=> $value,
					'updated_date'	=> time()
				);
				
				if(Coupon::update($data))
				{
					$this->_redirect(BASE_URL .'/adm/coupon/coupon-list');
				}
			}
		}
	
		$this->view->coupon = $coupon;
	}
	
	/**
	 *
	 * add photo action
	 */
	public function addCouponAction()
	{
		$request = $this->getRequest();
		
		if ($this->getRequest()->isPost())
		{			
			$couponName= $request->getParam('coupon_name');
			$couponName = My_Zend_Globals::strip_word_html(trim($couponName), '');
			$couponName = htmlspecialchars($couponName);
			
			$status = intval($this->_getParam('status'));
			
			$price= $request->getParam('price');
			$price = floatval(str_replace('.', '', $price));
			
			$value= $request->getParam('value');
			$value = floatval(str_replace('.', '', $value));
			
			if(!empty($couponName) && !empty($price))
			{
				$data = array(
					'coupon_name'	=> $couponName,
					'status'		=> $status,
					'price'			=> $price,
					'value'			=> $value,
					'created_date'	=> time(),
					'updated_date'	=> time()
				);
				
				$couponId = Coupon::insert($data);
				
				if($couponId)
				{
					$this->_redirect(BASE_URL .'/adm/coupon/coupon-list');
				}
			}
		}
	}
	
	/**
	 *
	 * delete coupon
	 */
	public function deleteCouponAction()
	{
		$id = $this->_getParam('id', 0);
		$id = intval($id);
			
		$coupon = Coupon::getCoupon($id);
		
		if(empty($coupon))
		{
			echo 0; exit;
		}
		
		$rs = Coupon::delete($id);
		
		echo 1; exit;
	}
	
}