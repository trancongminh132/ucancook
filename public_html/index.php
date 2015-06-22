<?php
//error_reporting(E_ALL);
//Setup Include Paths
define('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
define('APPLICATION_PATH', realpath(DOCUMENT_ROOT.'/../application'));
define('DATA_PATH', realpath(DOCUMENT_ROOT.'/../data'));
define('LOGGERS_PATH', realpath(DOCUMENT_ROOT.'/../logs'));
define('LIBS_PATH', realpath(DOCUMENT_ROOT . '/../library'));
define('IMPORT_TMP_PATH', realpath(DOCUMENT_ROOT . '/upload'));
define('PAGES_PATH', realpath(DOCUMENT_ROOT.'/static_pages'));
define('UPLOAD_PATH', realpath(DOCUMENT_ROOT . '/uploads'));
//$_SERVER['Site_ENV'] = 'production';

//echo'Website is offline.Please come back later.';die;

if($_SERVER['HTTP_HOST'] == '103.27.62.136')
{
	// redirect ip canolization
	header ('HTTP/1.1 301 Moved Permanently');
	header("Location: http://www.ucancook.vn". $_SERVER['REQUEST_URI']);
}

if($_SERVER['HTTP_HOST'] == 'ucancook.vn')
{
	// redirect non www to www
	header ('HTTP/1.1 301 Moved Permanently');
	header("Location: http://www.". $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'www.ucancook.vn' && ($_SERVER['REQUEST_URI'] == '/' || (empty($_GET) && strlen($_SERVER['REQUEST_URI']) < 4)) && file_exists(PAGES_PATH .'/index.html'))
{
	include PAGES_PATH .'/index.html';
	exit();
}

$fileName = $_SERVER['REQUEST_URI'];
$fileName = str_replace($_SERVER['QUERY_STRING'], '', $fileName);
$fileName = str_replace('?', '', $fileName);

if(!empty($fileName) && $fileName != '/' && file_exists(PAGES_PATH . $fileName))
{
	include PAGES_PATH . $fileName;
	exit();
}

set_include_path(implode(PATH_SEPARATOR,array(
    LIBS_PATH,
    get_include_path()
)));

//Load bootstrap
require_once APPLICATION_PATH . '/configs/defines.php';

try
{
	//Get options
	$options = array(
	    'bootstrap' => array(
            'path'   => APPLICATION_PATH.'/Bootstrap.php',
            'class'  => 'Bootstrap'
        ),
        'phpSettings' => array(
            'display_startup_errors' => (APPLICATION_ENVIRONMENT == 'localhost')?1:0,
            'display_errors'         => (APPLICATION_ENVIRONMENT == 'localhost')?1:0
        )
	);

	//Load Application
    require_once 'Zend/Application.php';
    $application = new Zend_Application(
        APPLICATION_ENVIRONMENT,
        $options
    );

    //Display
    $application->bootstrap()->run();
}
catch(Zend_Exception $exception)
{
    echo '<html><body><center>'
        .'An exception occured while bootstrapping the application.';
    echo APPLICATION_ENVIRONMENT.'<br/>';
    if(APPLICATION_ENVIRONMENT == 'development')
    {
        echo '<br /><br />'.$exception->getMessage().'<br />'
        .'<div align="left">Stack Trace:'
        .'<pre>'.$exception->getTraceAsString().'</pre></div>';
    }
    echo '</center></body></html>';
	//exit(1);
}