<?php
class ErrorController extends Zend_Controller_Action
{
	function init()
	{
		$this->getResponse()->setHttpResponseCode(404);
	}
	
	/**
	 * Error handle action
	 */
    public function errorAction()
    {
    	$error = $this->_getParam('error_handler');
    	
        switch(get_class($error->exception)) {
          case 'PageNotFoundException':
          	if(APPLICATION_ENVIRONMENT == 'production')
          	{
            	$this->_forward('page-not-found');
          	}       
          	
            break;
     
          default:
            //put some default handling logic here
          	if(APPLICATION_ENVIRONMENT == 'production')
          	{
          		
          	}
            break;
        }
        
    }
    
    public function pageNotFoundAction()
    {

    }
}