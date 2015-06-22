<?php
class My_Zend_Authenticate
{ 
	/**
	 * string username
	 */
    public $username ;
    
    /**
     * Zing_App_Auth
     * @var Zing_App_Auth
     */
	protected $auth ;
	
	/**
	 * Ham khoi tao
	 * @return boolean
	 */
	public function __construct()
	{		
		$storageAdapter = new Zing_Auth_Storage_Session(
								array('host'=>'10.30.12.8','port'=>9090)
						  );
		
        $this->auth = new Zing_App_Auth();
        $this->auth->setStorage($storageAdapter);          	
	}
	
	/**
	 * Ham huy
	 * @return boolean
	 */
	public function __destruct()
	{
		$this->auth = null ;
	}
	
	/**
	 * Ham kiem tra login
	 * @return boolean
	 */
	public function isLogged() 
	{			
		try {
			$bool = $this->auth->isLogged() ;	

			//Neu chua Login hoac mat cookie Login
			if($bool == false) 
			{
				return false ;				
			}
			
			//Da login thi setup nhung thong tin can thiet
			$identity = $this->auth->getIdentity();					
			$this->username = isset($identity['username']) ? $identity['username'] : "";
				        
	        return true ;
		}
		catch(Exception $ex) 
		{
			return false ;						
		}
		
		return false ;
	}
}