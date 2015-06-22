<?php
class My_Zend_Plugin_Module extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $moduleName = $request->getModuleName();
        
        !defined('MODULE_NAME') && define('MODULE_NAME', $moduleName);
    }
}