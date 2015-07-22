<?php

class AjaxController extends Zend_Controller_Action
{
	function init()
	{		
		$this->_helper->layout->disableLayout();
    	
	}
	
	/**
	 * 
	 * check email register
	 */
	public function checkEmailAction()
    {
    	$this->_helper->layout->disableLayout();
    	$email = $this->_getParam('email', '');
    	$userId = $this->_getParam('user_id', 0);
    	$isNewsLetter = $this->_getParam('is_newsletter', 0);
    	
    	$email = trim($email);
    	$userId = intval($userId);
    	
    	if(empty($email))
    	{
    		My_Zend_Globals::returnJson(1);
    	}
    	
    	$filters = array(
    		'email'	=> $email
    	);
    	
    	$checkEmail = UserSolr::selectUserList($filters, 0, 1);
    
    	if($checkEmail['total'] > 0)
    	{    		
    		if($checkEmail['data'][0]['user_id'] == $userId && $isNewsLetter == 0)
    		{
    			My_Zend_Globals::returnJson(0);
    		}
    		
    		My_Zend_Globals::returnJson(1);
    	}
    	
    	My_Zend_Globals::returnJson(0);
    	exit;
    }

    /**
     * 
     * get data cart
     */
    public function getDataCartAction()
    {
    	$listProductCart = array();
    	$arrayReturn = array();
    	$dataFlip = array();
    	
    	if(isset($_COOKIE['ucancook_cart']))
    	{
    		$cookieCart = $_COOKIE['ucancook_cart'];
    		if(!empty($cookieCart))
    		{
    			$cookieCart = explode(';', $cookieCart);
    			
    			foreach ($cookieCart as $cookieItem)
    			{
    				if(!empty($cookieItem))
    				{
    					$cookieItemTmp = explode(',', $cookieItem);
    					$id = $cookieItemTmp[0];
    					$item = array();
    					$item['type'] = $cookieItemTmp[1];
    					$item['quantity'] = $cookieItemTmp[2];
    					$item['id'] = $id;
    					
    					switch($cookieItemTmp[1])
    					{
    						case TYPE_GIFT:
    							$coupon = Coupon::getCoupon($id);
    							$item['name'] = 'Phiếu quà tặng '.My_Zend_Globals::numberFormat($coupon['value']);
    							$item['price'] = $coupon['price'];
    							$item['more_info'] = $cookieItemTmp[3];
    							break;
    						case TYPE_DISH:
    							$dish = Dish::getDish($id);
    							$item['name'] = $dish['name'];
    							if(!empty($dish['special_price']))
    								$item['price'] = $dish['special_price'];
    							else
    								$item['price'] = $dish['price'];
    							$item['image'] = My_Zend_Globals::getThumbImage($dish['image']);
    							$item['more_info'] = $cookieItemTmp[3];
    							break;
    						case TYPE_INGREDIENT:
    							$ingredient = Ingredient::getItem($id);
    							$item['name'] = $ingredient['name'];
    							if(!empty($ingredient['special_price']))
    								$item['price'] = $ingredient['special_price'];
    							else 
    								$item['price'] = $ingredient['price'];
    							$item['image'] = My_Zend_Globals::getThumbImage($ingredient['image']);
    							$item['more_info'] = "";
    							break;
    					}
    					
    					$arrayReturn[] = $item;
    				}
    			}
    		}
    		
    		$dataFlip = $arrayReturn;
    		
    		if(!empty($arrayReturn))
    		{
    			$dataFlip = array();
		    	foreach($arrayReturn as $val)
		    	{
		    		$key = $val['id'].'.'.$val['type'];
		    		if($val['type'] == TYPE_GIFT)
    				{
			    		if(isset($dataFlip[$key]))
			    		{
			    			$dataFlip[$key]['quantity']++;
			    			$dataFlip[$key]['more_info'] = $dataFlip[$key]['more_info'].';'.$val['more_info'];
			    			$dataFlip[$key]['str_quantity'] .= ';'.$val['quantity']; 
			    		}
			    		else{
			    			$dataFlip[$key] = $val;
			    			$dataFlip[$key]['str_quantity'] = $val['quantity'];
			    		}
    				}else{
    					$dataFlip[$key] = $val;
    				}
		    	}
    		}
    	}
    	
    	unset($dataReturn);
    	$dataFlip = array_values($dataFlip);
    	echo Zend_Json::encode($dataFlip);die; 
    }
    
    /**
     * 
     * insert shipping address
     */
    public function insertOrderAddressAction()
    {
    	$rs = 0;
    	$orderName = $this->_getParam('full_name', '');
    	$orderName = My_Zend_Globals::strip_word_html($orderName, '');
    
    	$orderEmail = $this->_getParam('email_order', '');
    	$orderEmail = My_Zend_Globals::strip_word_html($orderEmail, '');
    
    	$orderPhone = $this->_getParam('order_phone', '');
    	$orderPhone = My_Zend_Globals::strip_word_html($orderPhone, '');
    
    	$orderCity = intval($this->_getParam('order_city', ''));
    	$orderDistrict = intval($this->_getParam('order_district', ''));
    
    	$orderAddress = $this->_getParam('order_address', '');
    	$orderAddress = My_Zend_Globals::strip_word_html($orderAddress, '');
    
    	$orderNote = My_Zend_Globals::strip_word_html(trim($this->_getParam('order_note', '')), '');
    
    	$companyName = $this->_getParam('company_name', '');
    	$companyName = My_Zend_Globals::strip_word_html($companyName, '');
    		
    	if(empty($orderName) || empty($orderAddress) || empty($orderPhone) || empty($orderEmail)  || empty($orderCity) || empty($orderDistrict)){
    		echo Zend_Json::encode(array('rs' => 0)); exit;
    	}
    
    	$dataOrder = array(
    		'user_id' => LOGIN_UID,
    		'order_name'	 => $orderName,
    		'order_phone'	 => $orderPhone,
    		'order_city'	 => $orderCity,
    		'order_district' => $orderDistrict,
    		'order_address'	 => $orderAddress,
    		'order_note'	 => $orderNote,
    		'company_name'   => $companyName,
    		'order_email'	 => $orderEmail,
    		'created_date'	 => time(),
    		'updated_date'	 => time()
    	);
        	
    	ShippingAddress::setConfig($dataOrder);
    	
    	echo Zend_Json::encode(array('rs' => 1)); exit;
    }
    
    /**
     * toggle no interest user taste
     * 
     */
    public function togglenointerestAction()
    {
    	$status = intval($this->_getParam('status'));
    	$tasteId = intval($this->_getParam('id'));
    	
    	if(empty($status) || empty($tasteId))
    	{
    		echo Zend_Json::encode(array('error' => 1)); exit;
    	}
    	
    	$data = array(
    		'taste_id' => $tasteId,
    		'user_id' => LOGIN_UID
    	);
    	
    	if($status == 1)
    		Dish::insertUserTaste($data);
    	else 
    		Dish::deleteUserTaste($data);
    	
    	echo Zend_Json::encode(array('error' => 0)); exit;
    }
    
    /**
     * 
     * register email newsletter
     */
    public function registerEmailNewsletterAction()
    {
    	$request = $this->getRequest();  
    
    	if ($request->isPost())
    	{
    		$email = $request->getParam('email', '');
    		$email = My_Zend_Globals::strip_word_html($email, '');
    
    		if(empty($email) || !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email))
    		{
    			echo Zend_Json::encode(array('error' => 1)); exit;
    		}
    		
    		$isExitsEmail = NewsLetter::isExitsEmailRegNewsLetter($email);
    		
    		if(!empty($isExitsEmail))
    		{
    			echo Zend_Json::encode(array('error' => 2)); exit;
    		}
    		
    		$ip = My_Zend_Globals::getAltIp();
    
    		$data = array(
    			'user_id' => LOGIN_UID,
    			'email' => $email, 
    			'ip' => $ip,
    			'created_date' => time()   			
    		);

    		$register = NewsLetter::insert($data);
    
    		if($register)
    		{
    			echo Zend_Json::encode(array('error' => 0)); exit;
    		}
    	}
    }
    
    /**
     * 
     * generate url checkout
     * 
     */
    public function generateurlcheckoutAction()
    {
    	$typePayment = $this->_getParam('type_payment');
    	$typePayment = intval($typePayment);
    	
    	$orderId = $this->_getParam('order_id');
    	$orderId = intval($orderId);
    	
    	$orderDetail = ProductOrders::getProductOrder($orderId);
    	$buyer = User::getUser($orderDetail['buyer_id']);
    	
    	$listProduct = ProductOrders::getListProductOfOrder($orderId);
    	$quantity = 0;
    	$transactionInfo = '';
    	foreach($listProduct as $item)
    	{
    		$quantity += $item['quantity'];
    		if($item['type'] == TYPE_DISH)
    		{
    			$dish = Dish::getDish($item['item_id']);
    			$transactionInfo.= $dish['name'].' - '. $item['quantity'].' phần, ';
    		}elseif($item['type'] == TYPE_GIFT)
    		{
    			$coupon = Coupon::getCoupon($item['item_id']);
    			$transactionInfo.= $coupon['coupon_name'].' - '. $item['quantity'].' phiếu, ';
    		}else{
    			$ingredient = Ingredient::getItem($item['item_id']);
    			$transactionInfo.= $ingredient['name'].' - '. $item['quantity'].' phần, ';
    		}
    	}
    	unset($listProduct);
    	
    	if($typePayment == 1)
    	{
    		$returnUrl = BASE_URL.'/order/success?order_id='.$orderId;
    		$url = NganLuong::buildCheckoutUrlExpand($returnUrl, 'trancongminh132@gmail.com', 'Món ăn 5 phần', $orderDetail['order_code'], $orderDetail['amount_total'], 'vnd', $quantity, 0, 0, 0, $orderDetail['shipping_cost'], $orderDetail['order_note'], $buyer['email'], '');
    	
    	}else if($typePayment == 2)
    	{
    		$returnUrl = BASE_URL.'/order/success?order_id='.$orderId;
    		$urlCancel =  BASE_URL.'/order/checkout';
    		$url = BaoKim::createRequestUrl($orderDetail['order_code'], 'trancongminh132@gmail.com', $orderDetail['amount_total'], $orderDetail['shipping_cost'], 0, $transactionInfo, $returnUrl, $urlCancel, $returnUrl);
    		 
    	}
    	
    	echo Zend_Json::encode(array('error' => 0, 'url' => $url));die;
    }
}