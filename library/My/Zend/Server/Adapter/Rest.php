<?php
class My_Zend_Server_Adapter_Rest extends My_Zend_Server_Adapter_Abstract
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
    	parent::__construct();
    }
	
    /**
     * Destructor
     *
     */
	public function __destruct()
	{
		parent::__destruct();
	}	
	
	/**
	 * Handle class
	 *
	 */
	public function handle()
	{		
		try
		{
			$funcArg=array();
			$result=array();
			$errorCode=0;
			$errorMessage="Successful.";
			$request = $_REQUEST;			
			//Check method
			if (isset($_REQUEST['method'])) 
			{
				$_method=$_REQUEST['method'];
				$class = new ReflectionClass($this->_class);
				if($class->hasMethod($_method) && 
				   !in_array($_method, self::$magicMethods))
				{
					$request_keys = array_keys($request);
					array_walk($request_keys, array(__CLASS__, "lowerCase"));
					$request = array_combine($request_keys, $request);
					$method=$class->getMethod($_method);
					$params=$method->getParameters();
					$number = count($params);
					
					if($number > 0)
					{
						for($i=0;$i<$number;$i++)
						{
							$paramName=strtolower($params[$i]->getName());
							$paramIndex=$params[$i]->getPosition();
							if (!isset($request[$paramName])) 
							{
								if ($params[$i]->isDefaultValueAvailable()) 
								{
									$paramValue = $params[$i]->getDefaultValue();
								} 
								else 
								{
									throw new My_Zend_Server_Exception('Required parameter "'.$paramName.'" is not specified.');
								}
							} 
							else 
							{
								$paramValue = $request[$paramName];
							}
							$funcArg[$paramIndex]=$paramValue;
						}
					}
					
					if ($method->isStatic())
					{
						if($number > 0)
						{
							$result=call_user_func_array(array($this->_class,$_method),
						                                $funcArg);
						}
						else 
						{							
							$result=call_user_func(array($this->_class,$_method));
						}
					} 
					elseif($method->isPublic())
					{
						$instance=$class->newInstance();
						$result=$method->invokeArgs($instance, $funcArg);
					}
				}
				else 
				{
					throw new My_Zend_Server_Exception('Request method not found');
				}

			 }
			 else
			 {
				throw new My_Zend_Server_Exception('No method given.');
			 }
		}
		catch(Exception $e)
		{
			$errorMessage=$e->getMessage();
			$errorCode=1;
			$result=array();
		}

		//Format data	
		$return = Zend_Json::encode($result);
				
		//Return result
       	echo $return;       		
	}
}