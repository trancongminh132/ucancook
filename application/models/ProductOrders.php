<?php
class ProductOrders
{
	const _TABLE_PRODUCT_ORDERS = 'product_orders';
	const _TABLE_PRODUCT_ORDER_DETAIL = 'product_order_detail';
	 
	public static $listOrderStatus = array(
		'1'		=> 'Đơn hàng mới',
		'2'		=> 'Đã hoàn thành',
		'3'		=> 'Đã hủy',
		'4'		=> 'Đã thanh toán'
	);
	 
	public static	$_paymentType 	= array(
		4	=> 'COD - Thanh toán khi giao hàng',
		3   => 'Thanh toán qua ví điện tử Payoo',
		2   => 'Thanh toán qua Bảo kim',
		1	=> 'Thanh toán qua Ngân lượng'
	);
	
	/**
     * Init data of product orders
     * @param array $data
     */
    public static function initProductOrders($data)
    {        
        $fields = array('order_id', 'buyer_id', 'order_name', 'order_phone', 'order_city', 'order_district', 'order_note', 'order_status',
        				'order_address', 'payment_type', 'created_date', 'updated_date', 'order_code', 'amount_total', 'shipping_cost');
		
        $rs = array();
        
        foreach($fields as $field)
        {
            if(isset($data[$field]))
            {
                $rs[$field]  = $data[$field];
            }
        }
                
        return $rs;
    }
    
	/**
     * Init data of product order detail
     * @param array $data
     */
    public static function initProductOrderDetail($data)
    {        
        $fields = array('id', 'order_id', 'item_id', 'quantity', 'price', 'type', 'more_info', 'created_date');
		
        $rs = array();
        
        foreach($fields as $field)
        {
            if(isset($data[$field]))
            {
                $rs[$field]  = $data[$field];
            }
        }
                
        return $rs;
    }
    
	/**
     * Insert Product Orders
     * @param array $data
     */
    public static function insertProductOrders($data) {
        //Init data
        $data = self::initProductOrders($data);

        if ($data === false) {
            return false;
        }

        //Get db instance
        $storage = My_Zend_Globals::getStorage();

        // Insert data
        $rs = $storage->insert(self::_TABLE_PRODUCT_ORDERS, $data);
        
    	if($rs)
		{
			$rs = $storage->lastInsertId();
		}
        // Return
        return $rs;
    }
    
	/**
     * Insert Product Order Detail
     * @param array $data
     */
    public static function insertProductOrderDetail($data) {
        //Init data
        $data = self::initProductOrderDetail($data);

        if ($data === false) {
            return false;
        }

        //Get db instance
        $storage = My_Zend_Globals::getStorage();

        // Insert data
        $rs = $storage->insert(self::_TABLE_PRODUCT_ORDER_DETAIL, $data);
        
        // Return
        return $rs;
    }
  
    /**
     * Update Product Orders
     * @param array $data
     */
    public static function updateProductOrders($data) 
    {
        $data = self::initProductOrders($data);

        if ($data === false) {
            return false;
        }

        //Get db instance
        $storage = My_Zend_Globals::getStorage();
       
        //Update data
        $rs = $storage->update(self::_TABLE_PRODUCT_ORDERS, $data, 'order_id=' . $data['order_id']);
       
        //Return
        return $rs;
    }
    
    /**
     * get list of product order
     * @param array $data
     */
    public static function getListOrderByUser($filter = array(), $offset, $limit){

        $storage = My_Zend_Globals::getStorage();
    	
    	$table = self::_TABLE_PRODUCT_ORDERS;
    	 
    	//Query data from database
    	$select = $storage->select()
			    	->from($table,'*')
			    	->order('order_id DESC')
			    	->limit($limit, $offset);
    	
		if(isset($filter['buyer_id']) && !empty($filter['buyer_id']) && $filter['buyer_id'] != 'adm'){
    		$select->where('buyer_id = ?', $filter['buyer_id']);
    	} 
    	
    	if(isset($filter['order_status']) && $filter['order_status'] != 'all'){
    		$select->where('order_status = ?', $filter['order_status']);
    	}
    	
    	if(isset($filter['order_code']) && !empty($filter['order_code'])){
    		$select->where('order_code = ?', $filter['order_code']);
    	}
    	
    	if(isset($filter['order_phone']) && !empty($filter['order_phone'])){
    		$select->where('order_phone = ?', $filter['order_phone']);
    	}
    	
    	if(isset($filter['order_name']) && !empty($filter['order_name'])){
    		$select->where('order_first_name like "%'. $filter['order_name'].'%" OR order_m_i like "%'. $filter['order_name'].'%" OR order_last_name like "%'. $filter['order_name'].'%" ');
    	}
    	
    	if(isset($filter['order_address']) && !empty($filter['order_address'])){
    		$select->where('order_address_1 like "%'. $filter['order_address'].'%" OR order_address_2 like "%'. $filter['order_address'].'%"');
    	}
 
    	if(isset($filter['from_date']) && !empty($filter['from_date'])){
    		$select->where('created_date >= ?', $filter['from_date']);
    	}
    	
    	if(isset($filter['end_date']) && !empty($filter['end_date'])){
    		$select->where('created_date <= ?', $filter['end_date']);
    	}
    	
    	$listOrders = $storage->fetchAll($select);
    	 	
    	return $listOrders;
    }
    
    /**
     * get total of product order
     * @param array $data
     */
	public static function getTotalOrder($filter = array())
    {
    	$storage = My_Zend_Globals::getStorage();
    	 
    	$table = self::_TABLE_PRODUCT_ORDERS;
    	 
    	//Query data from database
    	$select = $storage->select()
    		->from($table,'count(order_id) as total');
    	
    	if(isset($filter['buyer_id']) && !empty($filter['buyer_id']) && $filter['buyer_id'] != 'adm'){
    		$select->where('buyer_id = ?', $filter['buyer_id']);
    	} 
    	
    	if(isset($filter['order_status']) && $filter['order_status'] != 'all'){
    		$select->where('order_status = ?', $filter['order_status']);
    	}
    	
    	if(isset($filter['order_code']) && !empty($filter['order_code'])){
    		$select->where('order_code = ?', $filter['order_code']);
    	}
    	
    	if(isset($filter['order_phone']) && !empty($filter['order_phone'])){
    		$select->where('order_phone = ?', $filter['order_phone']);
    	}
    	
    	if(isset($filter['order_name']) && !empty($filter['order_name'])){
    		$select->where('order_first_name like "%'. $filter['order_name'].'%" OR order_m_i like "%'. $filter['order_name'].'%" OR order_last_name like "%'. $filter['order_name'].'%" ');
    	}
    	
    	if(isset($filter['order_address']) && !empty($filter['order_address'])){
    		$select->where('order_address_1 like "%'. $filter['order_address'].'%" OR order_address_2 like "%'. $filter['order_address'].'%"');
    	}

    	$data = $storage->fetchRow($select);
    
    	return $data['total'];
    }
    
	/**
     * Select product order
     * @param int $orderId
     */
    public static function getProductOrder($orderId) 
    {
    	try
    	{
	    	$orderId = intval($orderId);    	
	    	if($orderId == 0)
	    		return false;
	    	    
	    	//Get db instance
	    	$storage = My_Zend_Globals::getStorage();
	
	    	//Query
	    	$select = $storage->select()
	    					->from(self::_TABLE_PRODUCT_ORDERS, '*')
	    					->where('order_id = ?', $orderId);
	    	
	    	$data = $storage->fetchRow($select);
	
	        return $data;
	        
    	}catch(Exception $e)
    	{
    		My_Zend_Logger::log('ProductOrders::getProductOrder -- '.$e->getMessage());
    		return false;
    	}
    }
    
    /**
     * Select list product of order
     * @param int $orderId
     */
	public static function getListProductOfOrder($orderId)
	{
		$orderId = intval($orderId);
		if($orderId == 0)
			return false;
			
    	$storage = My_Zend_Globals::getStorage();
    	 
    	$table = self::_TABLE_PRODUCT_ORDER_DETAIL;
    	 
    	//Query data from database
    	$select = $storage->select()
			    	->from($table,'*')
			    	->where('order_id = ?', $orderId);
    	
    	$listProducts = $storage->fetchAll($select);
    	 	
    	return $listProducts;
    }
    
    /**
     * get total value of order
     * @param int $orderId
     */
    public static function getTotalValueOfOrder()
    {
    	try
    	{
	    	$storage = My_Zend_Globals::getStorage();
	    
	    	$table = self::_TABLE_PRODUCT_ORDER_DETAIL;
	    
	    	//Query data from database
	    	$select = $storage->select()
					    	->from($table,'sum(amount_total)')
					    	->where('order_status = ?', 2);
	    	 
	    	$total = $storage->fetchCol($select);
	    	 
	    	$total = intval($total['total']);
	    	
	    	return $total;
    	}
    	catch(Exception $e)
    	{
    		return false;
    	}	    	
    }

    /**
     * get total value of order
     * @param int $orderId
     */
    public static function getTotalValueOfOrderDashboard()
    {
    	try
    	{
	    	$storage = My_Zend_Globals::getStorage();
	    
	    	$table = self::_TABLE_PRODUCT_ORDER_DETAIL;
	    
	    	//Query data from database
	    	$select = $storage->select()
					    	->from($table,'sum(amount_total)')
					    	->where('order_status = ?', 0);
	    	 
	    	$total = $storage->fetchCol($select);
	    	 
	    	$total = intval($total['total']);
	    	
	    	return $total;
    	}
    	catch(Exception $e)
    	{
    		return false;
    	}	    	
    }
    
    public static function mapOrderName($orderFirstName, $orderLastName, $orderMIName){
    	return $orderLastName. ' '.$orderMIName. ' '.$orderFirstName;   	
    }
    
    public static function beginDate($time)
    {
    	if(empty($time))
    		return 0;
    	return strtotime(date('Y', $time) .'-'. date('m', $time) .'-'. date('d', $time) .' 00:00:00');
    }
    
    public static function endDate($time)
    {
    	if(empty($time))
    		return 0;
    	return strtotime(date('Y', $time) .'-'. date('m', $time) .'-'. date('d', $time) .' 23:59:59');
    }
    
}?>