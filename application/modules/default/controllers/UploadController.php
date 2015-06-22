<?php

class UploadController extends Zend_Controller_Action
{
    public function handlerAction()
    {    	
    	echo '<script type="text/javascript">document.domain = "' . DOMAIN . '";</script>';
        $this->getHelper('layout')->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
        $params = $this->_getAllParams();

        if(!isset($params['resp'])) exit();
        
        $resp = $params['resp'];        
        echo $resp;
        exit();
    }
    
    public function getsignkeyAction()
    {
    	$width = $this->_getParam('width', 0);
    	$height = $this->_getParam('height', 0);
    	$maxSize = $this->_getParam('max_size', 0);
    	
    	$width = intval($width);
    	$height = intval($height);
    	$maxSize = intval($maxSize);
    	
    	$data = array(
    				'time'	=> time(),
    				'seckey'	=> My_Zend_Globals::generatePhotoSignKey(time(), $width, $height, $maxSize)
    	);
    	
    	echo Zend_Json::encode($data);
    	exit();
    }
}