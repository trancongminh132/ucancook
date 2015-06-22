<?php
class My_Zend_Logger
{	
	/**
	 * Write log message
	 *
	 * @param string $message
	 * @return boolean
	 */
	public static function log($message, $br = false)
	{
		try {
			//Check message
			if(empty($message))
			{
				return false;
			}
	
			$config = My_Zend_Globals::getConfiguration();
			
			$logger = new Zend_Log(); 
	        $writer = new Zend_Log_Writer_Stream($config->logger->path. date('mY', time()) .'.log');
	        $logger->addWriter($writer);
	    		
	        //Cleanup
	        unset($writer);
			
			//Write log
			$message = '(IP: '. My_Zend_Globals::getAltIp() .') ++++===============  '.$message.'  ===============+++';
		    if($br)
			{
				$message .= "\n\n";
			}
			$logger->log($message, Zend_Log::INFO);		
			
			return true;
		}
		catch(Exception $ex)
		{
			return false;
		}
	}
}