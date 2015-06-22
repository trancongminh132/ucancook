<?php
class My_Zend_SMS
{
	private $_client = null;
	private static $_instances = null;
	//private $_path = 'http://10.199.38.101/axis/services/AdsRMTNEW?wsdl'; // dev	
	//private $_key = '@123$456'; // dev
	
	//private $_path = 'http://10.30.29.100/axis/services/AdsRMTNEW?wsdl'; // live
	private $_path = 'http://mvas.framework/axis/services/AdsRMTNEW?wsdl'; // live
	private $_key = 'R9ngVypfowNhLOQBih1J9NQaaRKpTDIg'; // live
	
	public function __construct($options)
	{
		try {
			$this->_client = new Zend_Soap_Client($this->_path);
		}
		catch (Exception $e)
		{
			var_dump($e);
		}
	}
	
	public function __destruct()
	{
		
	}
	
	public static function getInstance($options)
	{		
		if (empty(self::$_instances))
			self::$_instances = new self($options);
			
		return self::$_instances;
	}

	public function genRequestId()
	{
		// format PrefixyyMMddhhmmssSSS
		$utimestamp = microtime(true);
		$timestamp = floor($utimestamp);
	    $milliseconds = round(($utimestamp - $timestamp) * 1000);

		return '87' . date('ymdHis'). $milliseconds;
	}
	
	public function send($number, $message, $serviceId = '6069', $commandCode='MUA123MT')
	{
		if(empty($number) || empty($message))
		{
			return false;
		}
		
		$requestId = $this->genRequestId();
		$sig = $this->_createSigKey($requestId, $number, $serviceId, $commandCode, $message, $this->_key);
		
		$params = array(
				'requestID' => $requestId,
				'userID' => $number,
				'message' => $message,
				'serviceID' => $serviceId,
				'commandCode' => $commandCode,
				'sig' => $sig,
		);

		try
		{
			return $this->_client->mtReceiver($requestId, $number, $serviceId, $commandCode, base64_encode($message), $sig);		
		} 		
		catch(Exception $e)
		{
			var_dump($e);
			return false;
		}
	}
	
	private function _createSigKey($requestID, $userId, $serviceId, $commandCode, $message, $secretKey)
	{
		return md5($requestID . $userId . $serviceId . $commandCode . $message . $secretKey);
	}
}