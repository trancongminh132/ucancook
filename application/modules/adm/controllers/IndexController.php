<?php
class Adm_IndexController extends Zend_Controller_Action
{   
    public function init()
    {
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        
    }

    /**
     *
     * Dashboard action
     */
    public function loginAction()
    {    	
    	$userId = defined('LOGIN_UID')?LOGIN_UID:0;
    	
    	if($userId > 0)
    	{
    		$this->_redirect(BASE_URL . '/adm');
    	}
    	
    	$this->_helper->layout->disableLayout();
    	
		$request = $this->getRequest();

		if($request->isPost())
		{
			$email = trim($request->getParam('email', ''));
			$password = trim($request->getParam('password', ''));
			
			$login = Admin::login($email, $password);
			
			if($login)
			{
				$this->_redirect(BASE_URL .'/adm');
			}
			else {
				$this->view->error_code = 1;
			}
		}
    }
    
    public function logoutAction()
    {
    	$auth = Zend_Auth::getInstance();
    	$auth->setStorage(new Zend_Auth_Storage_Session('Adm'));
    	
    	$auth->clearIdentity();
    	
    	header("Location: ". BASE_URL . '/adm/');
    	exit;
    }
    
    public function buildhomeAction()
    {
    	try{
    		$links = array('home' => BASE_URL .'/?buildhome=fuck', 'sitemap' => BASE_URL .'/sitemap_generator.php');
    		
    		foreach($links as $name => $url)
    		{	    		
	    		$curl = curl_init();
	    		curl_setopt($curl, CURLOPT_URL, $url);
	    		curl_setopt($curl, CURLOPT_HEADER, false);
	    		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
	    		curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, true);
	    		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
	    		$content = curl_exec($curl);
	    		$response = curl_getinfo( $curl );
	    		curl_close ( $curl );
	    		 
	    		$message .= 'Build '. $name .' - ';
	    		if($response['http_code'] == 200)
	    		{
	    			$message .= 'success. ';
	    		}
	    		else
	    		{
	    			$message .= 'fail';
	    		}
	    		$message .= "<br/>";
    		}
    	}
    	catch(Exception $e)
    	{
    		$message .= $e->getMessage();
    	}
    	
    	echo Zend_Json::encode(array('msg' => $message)); exit;
    }
    
    public function uploadsingleAction()
    {
    	$request = $this->getRequest();
    	if ($request->isPost()) 
    	{	
    		$data = $this->_request->getPost();
    		$file = isset($_FILES['files']) ? $_FILES['files'] : null;
    		
    		$upload = new Upload();
    		$destFolder = array('photo', date('Y'), date('m'));
    		$rsUpload = $upload->upload($file, $destFolder);
    		$rsUpload = $rsUpload[0];
    		if(!empty($rsUpload))
    		{
    			$return = array('error_code' => 0, 'data' => array('photo_src' => $rsUpload['url'], 'name' => $rsUpload['name']));
    		}else{
    			$return = array('error_code' => 1);
    		}
    		
    		$stringReturn = '<script type="text/javascript">document.domain = "'.DOMAIN.'";</script>'.Zend_Json::encode($return);
    		
    		echo $stringReturn;exit;
    	}
    }
    
    public function uploadAction()
    {
    	$request = $this->getRequest();
    	if ($request->isPost())
    	{
    		$data = $this->_request->getPost();
    		$file = isset($_FILES['files']) ? $_FILES['files'] : null;
    		
    		$upload = new Upload();
    		$destFolder = array('photo', date('Y'), date('m'));
    		$rsUpload = $upload->upload($file, $destFolder);
    		$rsUpload = $rsUpload[0];
    		if(!empty($rsUpload))
    		{
    			$return = array('error_code' => 0, 'data' => array('photo_src' => $rsUpload['url'], 'name' => $rsUpload['name']));
    		}else{
    			$return = array('error_code' => 1);
    		}
    
    		$stringReturn = '<script type="text/javascript">document.domain = "'.DOMAIN.'";</script>'.Zend_Json::encode($return);
    
    		echo $stringReturn;exit;
    	}
    }
}