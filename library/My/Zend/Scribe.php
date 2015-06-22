<?php
$GLOBALS['THRIFT_ROOT'] = LIBS_PATH.'/Apache/Thrift';
$GLOBALS['SCRIBE_ROOT'] = LIBS_PATH.'/Apache/Thrift/package/scribe';
require_once(LIBS_PATH.'/Apache/Thrift/package/scribe/scriber_thrift.php');
require_once(LIBS_PATH.'/Apache/Thrift/package/scribe/scriber_abstract.php');
require_once(LIBS_PATH.'/My/Zend/Globals.php');
class My_Zend_Scribe extends scriber_abstract
{
    //Category of logger
    const CATEGORY_REQUEST = 'ZINGAPP_123MUA';
    const CATEGORY_APP123_REQUEST = 'ZINGAPP_123MUA_REQUEST';
    
    //Serverip,requestdomain,clientip,meid,actionid,requestdate,logdata
    const LOG_FRIEND_REQUEST = "%s\t%s\t%s\t%s\t%s\t%s\t%s\t";
    
    //Serverip,requestdomain,clientip,meid,actionid,requestdate,logdata, refer, browser 
    const LOG_REQUEST_1 = "%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t\t%s\t%s\t%s";

    /**
     * Scribe instance
     * @var <object>
     */
    private static $_instance = null;
    
    /**
     * Scribe instance server 2
     * @var <object>
     */
    private static $_instance2 = null;

    /**
     * Constructor
     * @param <array> $config
     */
    public function __construct($config)
    {
		parent::__construct($config);
    }

    /**
     *
     * @param <array> $config
     * @return My_Zend_Logger
     */
    public static function getInstance()
    {
		if(self::$_instance == null)
		{
		   $config = Zend_Registry::get(APPLICATION_CONFIGURATION)->scriber->setting->toArray();
		   if(empty($config))
           {
               return null;
           }	       
		   $config['scribe_servers'] = explode(",",$config['scribe_servers']);
		   $config['scribe_ports'] = explode(",",$config['scribe_ports']);
		   		   
		   self::$_instance = new self($config);
		}
		return self::$_instance;
    }
    
	/**
     *
     * @param <array> $config
     * @return My_Zend_Logger
     */
    public static function getInstance2()
    {
		if(self::$_instance2 == null)
		{
		   $config = Zend_Registry::get(APPLICATION_CONFIGURATION)->scriber->setting->toArray();
		   if(empty($config))
           {
               return null;
           }	       
		   $config['scribe_servers'] = explode(",",$config['scribe_servers_2']);
		   $config['scribe_ports'] = explode(",",$config['scribe_ports']);
		   		   
		   self::$_instance2 = new self($config);
		}
		return self::$_instance2;
    }

    /**
     * Send log to scribe server
     * @param <int> $meid
     * @param <int> $actionid
     * @param <string> $logdata
     * @return <boolean>
     */
    public static function sendLog($meid,$actionid,$logdata='', $shopId=0, $categoryId=0, $productId=0)
    {
        try
        {
            //Get data
            $serverip = $_SERVER["SERVER_ADDR"];
            $requestdomain = $_SERVER["HTTP_HOST"];
            $clientip = My_Zend_Globals::getAltIp();
            $requestdate = time();

            //Set category logger
            $category = self::CATEGORY_REQUEST;
                        
            //Format message
            $message  = self::LOG_FRIEND_REQUEST;
            $message  = sprintf($message,$serverip,$requestdomain,$clientip,$meid,$actionid,$requestdate,$logdata);
            
        	//Send Log To Server 2
        	$result = self::sendLog2($meid, $actionid, $logdata, $shopId=0, $categoryId=0, $productId=0);
            
            //Get instance server 1
            $obj = self::getInstance();
                        
            //Check instance server 1
            if(!($obj instanceof scriber_abstract))
            {
                return false;
            }
            
            //Set log server 1
            return $obj->_sendLog($category,$message);
        }
        catch (Exception $ex)
        {        	
            //echo $ex->getMessage();
            return false;
        }
    }
    
	/**
     * Send log to scribe server
     * @param <int> $meid
     * @param <int> $actionid
     * @param <string> $logdata
     * @return <boolean>
     */
    public static function sendLog2($meid,$actionid,$logdata='',$shopId=0, $categoryId=0, $productId=0)
    {
        try
        {
            //Get data
            $serverip = $_SERVER["SERVER_ADDR"];
            $requestdomain = $_SERVER["HTTP_HOST"];
            $clientip = My_Zend_Globals::getAltIp();
            $referer = isset($_POST['refer']) ? $_POST['refer'] : '';
            $userAgent = isset($_POST['user_agent']) ? $_POST['user_agent'] : '';
            $requestdate = time();

            //Set category logger
            $category = self::CATEGORY_APP123_REQUEST;

            if($actionid == 999999)
            {
            	$return = 3;
            	setcookie('session2', '', $requestdate + 3600, '/', '.123mua.vn');
            }
            elseif(isset($_COOKIE['session1']))
            {
            	$return = 1;
            	$session1 = $_COOKIE['session1'];
            	
            	if(!isset($_COOKIE['session2']))
            	{
            		$session2 = md5($clientip . $rand . $requestdate);
            		setcookie('session2', $session2, $requestdate + 3600, '/', '.123mua.vn');
            	}
            	else
            	{
            		$session2 = $_COOKIE['session2'];
            	}
            	//setcookie('session2', md5($clientip . $requestdate), $requestdate + 3600, '/', '.123mua.vn');
            }
            else 
            {
            	$return = 0;
            	$rand = My_Zend_Globals::randWord(5);            	
            	$session1 = $requestdate;
            	$session2 = md5($clientip . $rand . $requestdate);
            	
            	setcookie('session1', $session1);
            	setcookie('session2', $session2, $requestdate + 3600, '/', '.123mua.vn');
            }
                                   
            //Format message
            $message  = self::LOG_REQUEST_1;
            $message  = sprintf($message,$serverip,$requestdomain,$clientip, $session1, $meid,$actionid,$requestdate,$shopId,$categoryId,$productId,$logdata, $session2, $return, $referer, $userAgent);
            
        	//Get instance server 2
            $obj = self::getInstance2();
                       
            //Check instance server 2
            if(!($obj instanceof scriber_abstract))
            {
                return false;
            }
            
            //Set log server 2
            return $obj->_sendLog($category,$message);
        }
        catch (Exception $ex)
        {        	
            //echo $ex->getMessage();
            return false;
        }
    }
}