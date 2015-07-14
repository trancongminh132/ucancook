<?php
define('ENVIRONMENT', 'env');
define('APPLICATION_ENVIRONMENT', isset($_SERVER['Site_ENV'])?$_SERVER['Site_ENV']:'development');
if(isset($_SERVER['HTTP_HOST']))
	define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST']);
elseif(isset($_SERVER['SERVER_NAME']))
	define('BASE_URL', 'http://'.$_SERVER['SERVER_NAME']);

define('CONTENT_URL', BASE_URL.'/content');
define('LOGIN_URL', BASE_URL.'/adm/index/login');
define('LOGIN_ADM_URL', BASE_URL.'/adm/index/login');
define('LOCKED_URL', BASE_URL.'/user_locked.html');
define('LOCKED_ADM_URL', BASE_URL.'/user_adm_locked.html');
define('DOMAIN', 'local-ucancook.vn');
define('CONFIGS_PATH', 'configs_path');
define('LOGGER_DUMP', 'logger_dump');
define('APPLICATION_CONFIGURATION', 'appConfig');
define('ADMIN_LOG_CONFIGURATION', 'adminlogConfig');
define('PROFILER_CACHE', 'PROFILER_CACHE');
define('RECORD_PER_PAGE', 30);

define('PAGE_SIZE', 8);
define('MAX_PRODUCT_ITEM', 24);

define('SYSTEM_USER', 'system_user');

// Facebook Info
define('FACEBOOK_APP_KEY', '344527302396637');
define('FACEBOOK_APP_SECKEY', 'f7c3d9e16fb4bc8a8907ac67b8dedd32');

define('PHONE_NUMBER', '098 9181 123'); // production
define('NOW', time());
define('TYPE_GIFT', 1);
define('TYPE_DISH', 2);
define('TYPE_INGREDIENT', 3);

define('CAT_ID_1', 1);
define('CAT_ID_2', 2);
define('CAT_ID_3', 3);
define('CAT_ID_4', 4);