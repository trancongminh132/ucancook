<?php
class My_Zend_Plugin_SEO extends Zend_Controller_Plugin_Abstract
{
    /**
     * Called after the router finishes routing the request.
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
    	/**
        //Get module
        $module = strtolower($request->getParam('module','default'));
        
        //Get controller
        $controller = strtolower($request->getParam('controller'));
        
        //Get action
        $action = strtolower($request->getParam('action'));
                
        //Set config path
        $configs_path = APPLICATION_PATH.'/configs/'.$module;

        //Load configuration
        $general_config = new Zend_Config_Ini($configs_path.'/general.ini', APPLICATION_ENVIRONMENT);

        //Registry config
        Zend_Registry::set(CONFIGS_PATH, $general_config);

        //Get Ini Configuration
        $configuration = My_Zend_Globals::getConfiguration();
        
        //Setup Include Paths
        set_include_path(implode(PATH_SEPARATOR,array(
                APPLICATION_PATH.'/models/'. $configuration->storage->adapter,
                get_include_path()
        )));
        
        set_include_path(implode(PATH_SEPARATOR,array(
                APPLICATION_PATH.'/models/'. $configuration->cassandra->adapter,
                get_include_path()
        )));
                
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $viewRenderer->view) {
            $viewRenderer->initView();
        }
        $view = $viewRenderer->view;
        $view->headTitle()->setSeparator(' | ');
        if( $controller != 'product' ) {
	        $view->headTitle('123mua.vn');
	        $view->headTitle()->append('Thuận mua Vừa bán');
	        $view->headMeta()->setName('keywords', 'mua,123mua,123 mua,mua hàng,mua hàng online,bán hàng,bán hàng online,máy vi tính,áo quần,thời trang,linh kiện,laptop,hàng giảm giá,hàng giá rẻ');
	        $view->headMeta()->setName('description', '123mua | website mua bán online hàng đầu Việt Nam');
        }
        if ($module=='default' && $controller=='product' && $action=='search')
        {
          $keyword     = $request->getParam('keyword', ''); 
          $category_id = (int)$request->getParam('category_id', 0);
          $category    = Category::selectCategory($category_id);                
          if($category)
          {            
          	$view->headTitle()->prepend($category['category_name']);
          	$view->headTitle()->append('123mua.vn');
          	$view->headTitle()->append('Thuận mua Vừa bán');
          	$page = $this->_request->getParam('page');
          	if($page > 1) {
          		$view->headTitle()->append('Trang '.$page);
          	}
         	if( isset($category['meta_keyword']{5}) ) {
            	$view->headMeta()->setName('keywords', $category['meta_keyword']);
            }
          	if( isset($category['meta_description']{5}) ) {
            	$view->headMeta()->setName('description', $category['meta_description']);
            }
          }
          elseif($keyword) 
          {
            $view->headTitle()->prepend($keyword);
          }  
        }
        elseif($module=='default' && $controller=='product' && $action=='detail')
        {
          $id = $request->getParam('id');
          
          //Check empty
          if(substr_count($id,'.'))
          {              
              $ids      = explode('.', $id);
              $me_id = $ids[0];
              $topic_id = $ids[1];
              
              $topic    = Topic::selectTopic($me_id, $topic_id);
              $category = Category::selectCategory($topic['category_id']);
              $view->headTitle()->prepend($category['category_name']);
              $view->headTitle()->prepend($topic['topic_name']);
              if( isset($category['meta_keyword']{5}) ) {
              	$view->headMeta()->setName('keywords', $category['meta_keyword']);
              }
              
              if(isset($topic['topic_summary']{5})) {
              	$view->headMeta()->setName('description', htmlspecialchars($topic['topic_summary']));
              } elseif($category['meta_description']{5}) {
              	$view->headMeta()->setName('description', $category['meta_description']);
              }
          }
        }
        elseif($module=='default' && $controller=='product' && $action=='productlist')
        {
          $me_id = (int)$request->getParam('me_id', 0);
          $shop  = User::selectShop($me_id);
          if($shop)
          {
            $view->headTitle()->prepend('Shop '.$shop['shop_name']);
          }
        }
        elseif($module=='default' && $controller=='index' && $action=='noel')
        {          
          $view->headMeta()->setName('description', '123mua | mua sam, mua ban, mua hang online, mua quan ao tren mang, ao khoat nam, ao khoat nu, giay nam 2010, giay nu, thoi trang Noel 2010, quan lot nam, converse, qua giang sinh handmade, quan kaki nam, qua tang giang sinh');
          $view->headTitle()->prepend('Sản phẩm Noel');
        }
        */        
        /*$view->headTitle()->append();
        $view->headMeta()->appendName('keywords', 'framework, PHP, productivity');*/
    }  
}