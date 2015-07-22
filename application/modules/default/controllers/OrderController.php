<?php
class OrderController extends Zend_Controller_Action 
{
	public function init()
	{
		
	}
	
	public function giftAction()
	{
		$coupon = Coupon::getList(array('status' => 1), 0, 10);
		$this->view->listCoupon = $coupon;
	}
	
	public function basketAction()
	{
		if(LOGIN_UID == 0)
		{
			$this->_redirect(BASE_URL.'/login.html?return='.BASE_URL.'/gio-hang.html');
		}
		
		$arrayReturn = array();
		
		if(isset($_COOKIE['ucancook_cart']))
		{
			$cookieCart = $_COOKIE['ucancook_cart'];
			if(!empty($cookieCart))
			{
				$shippingCost = 0;
				$totalPriceProduct = 0;
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
    							$item['str_quantity'] = $cookieItemTmp[2];
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
							
    					$totalPriceProduct += $item['price'] * $item['quantity'];  					
						$arrayReturn[$cookieItemTmp[1]][] = $item;
					}
				}
			}
		}
		
		if($totalPriceProduct > 500000)
			$shippingCost = 0;
		else 
			$shippingCost = 20000;
			
		$this->view->shippingCost = $shippingCost;		
		$this->view->arrayReturn = $arrayReturn;
	}
	
	public function successAction()
	{
		$this->getHelper('layout')->disableLayout();
		
		$orderId = $this->_getParam('order_id');
		$orderId=  intval($orderId);
	
		$orderDetail = ProductOrders::getProductOrder($orderId);
		
		if(empty($orderDetail) || LOGIN_UID != $orderDetail['buyer_id'])
		{
			$this->_redirect(BASE_URL);
		}
		
		$this->view->orderDetail = $orderDetail;
	}
	
	public function getdistrictAction()
	{
		require_once APPLICATION_PATH .'/configs/cities.php' ;
	
		global $_CITIES, $_DISTRICTS;
	
		$cityId = $this->_getParam('city_id', 0);
	
		$cityId = intval($cityId);
	
		$returnData = array();
	
		if(!isset($_DISTRICTS[$cityId]))
		{
			echo Zend_Json::encode($returnData);
			exit();
		}
	
		echo Zend_Json::encode($_DISTRICTS[$cityId]);
		exit;
	}
	
	public function checkoutAction()
	{
		$this->getHelper('layout')->disableLayout();
		
		if(LOGIN_UID == 0)
		{
			$this->_redirect(BASE_URL.'/login.html?return='.BASE_URL.'/checkout.html');
		}

		$arrayReturn = array();
		
		if(isset($_COOKIE['ucancook_cart']))
		{
			$cookieCart = $_COOKIE['ucancook_cart'];
			if(!empty($cookieCart))
			{
				$shippingCost = 0;
				$totalPriceProduct = 0;
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
								$item['str_quantity'] = $cookieItemTmp[2];
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
							
						$totalPriceProduct += $item['price'] * $item['quantity'];
						$arrayReturn[$cookieItemTmp[1]][] = $item;
					}
				}
			}
		}
		
		if($totalPriceProduct > 500000)
			$shippingCost = 0;
		else
			$shippingCost = 20000;
			
		$this->view->shippingCost = $shippingCost;
		$this->view->arrayReturn = $arrayReturn;
		
		$dataAddress = ShippingAddress::getConfig(LOGIN_UID);
		$this->view->dataAddress = $dataAddress;
	}
	
	/**
	 * 
	 * insert order
	 */
	public function insertOrderAction()
	{
		$rs = 0;
		$listItemCart = array();
		$cookieCart = $_COOKIE['ucancook_cart'];
		
		$amountTotal = 0;
		$totalPriceItem = 0;
		
		$city = intval($this->_getParam('payment_city', ''));
		$district = intval($this->_getParam('payment_district', ''));
		
		$address = $this->_getParam('payment_address', '');
		$address = My_Zend_Globals::strip_word_html($address, '');
		
		
		if(!empty($city) || !empty($district) || !empty($address)){
			$dataAddress = array(
				'payment_city'	 	=> $city,
				'payment_district' 	=> $district,
				'payment_address'	=> $address
			);
			
			setcookie('ucancook_payment_address', Zend_Json::encode($dataAddress), time() + (86400*30), "/");
		}
		
		if(!empty($cookieCart))
		{
			$shippingCost = 0;
			$cookieCart = explode(';', $cookieCart);
			foreach($cookieCart as $cookieItem)
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
							$item['str_quantity'] = $cookieItemTmp[2];
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
						
					$totalPriceItem += $item['price'] * $item['quantity'];
					$listItemCart[] = $item;
				}
			}
	
			if($totalPriceItem > 500000)
				$shippingCost = 0;
			else
				$shippingCost = 20000;
			
			if(!empty($listItemCart))
			{
				$dataAdress = ShippingAddress::getConfig(LOGIN_UID);
				
				if(empty($dataAdress)){
					echo Zend_Json::encode(array('rs' => 0)); exit;
				}
				
				$amountTotal   = $totalPriceItem + $shippingCost;
				$orderName     = $dataAdress['order_name'];
				$orderEmail    = $dataAdress['email_order'];
				$orderPhone    = $dataAdress['order_phone'];
				$orderCity     = $dataAdress['order_city'];
				$orderDistrict = $dataAdress['order_district'];
				$orderAddress  = $dataAdress['order_address'];
				$orderNote     = $dataAdress['order_note'];
				$typePayment = intval($this->_getParam('type_payment', ''));
				
				$dataOrder = array(
					'buyer_id'		 => LOGIN_UID,
					'order_name'	 => $orderName,
					'order_phone'	 => $orderPhone,
					'order_email'	 => $orderEmail,
					'order_city'	 => $orderCity,
					'order_district' => $orderDistrict,
					'order_address'	 => $orderAddress,
					'order_note'	 => $orderNote,
					'payment_type'   => $typePayment,
					'order_status'	 => ProductOrders::ORDER_STATUS_NEW,
					'created_date'	 => time(),
					'updated_date'	 => time()
				);
				 
				$dataOrder['order_code'] = 'PU'.date('Y').date('m').date('d').date('H').date('i').date('s');
				$dataOrder['amount_total'] = $amountTotal;
				$dataOrder['shipping_cost'] = $shippingCost;
				// note: unit, payment_type
				$orderId = ProductOrders::insertProductOrders($dataOrder);
				 
				if($orderId > 0)
				{
					foreach ($listItemCart as $product)
					{
						$dataProduct = array(
							'order_id'		=> $orderId,
							'item_id'		=> $product['id'],
							'quantity'		=> $product['quantity'],
							'price'			=> $product['price'],
							'type'			=> $product['type'],
							'more_info'		=> $product['more_info'],
							'created_date'	=> time()
						);
						
						ProductOrders::insertProductOrderDetail($dataProduct);
					}
					
					setcookie("ucancook_cart", "", time() + 3600, "/", DOMAIN);
	
					echo Zend_Json::encode(array('rs' => 1, 'order_id' => $orderId)); exit;
				}
			}
		}
		
		echo Zend_Json::encode(array('rs' => 0)); exit;
	}
}