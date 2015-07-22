<?php

class My_Zend_Globals
{
    /**
     * Logger instance
     */
    private static $logger = null;
   
    /**
     * Configuration instance
     */
    public static $configuration = null;
    
    /**
     * Admin Log configuration instance
     */
    private static $admin_log_configuration = null;
    /**
     * Storage instance
     * @var Zend_Db
     */
    private static $storage = null;
    
    /**
     * Admin log storage instance
     * @var Zend_Db
     */
    private static $admin_log_storage = null;
   
    /**
     *
     * Mail instance
     * @var Zend_Mail
     */
    private static $mail = null;
    /**
     * Solr instance
     * @var array Apache_Solr_Service
     */
    private static $solrList = null;
    /**
     * Job client
     * @var My_Zend_Job_Adapter_Gearman_Client
     */
    private static $jobClient = null;
    /**
     * Job function
     * @var string
     */
    public static $jobFunction = '';
    /**
     * List storage
     */
    private static $arrStorage = array();
   
    /**
     * List solr
     */
    private static $arrSorl = array();
    /**
     * List mail
     */
    private static $arrMail = array();    

    private static $caching = null;
    
    /**
     * Constructor
     *
     */
    private final function __construct()
    {

    }

    /**
     * Clone function
     *
     */
    private final function __clone()
    {

    }
  
    /**
     * Get job client
     */
    public static function getJobClient()
    {
        //Get Ini Configuration
        My_Zend_Globals::getConfiguration();
		
        //Get caching instance
        if (is_null(self::$jobClient))
        {
            self::$jobClient = My_Zend_JobClient::getInstance(self::$configuration->job->toArray());
        }

        //Set job function
        self::$jobFunction = self::$configuration->job->function;

        //Return caching
        return self::$jobClient;
    }

    /**
     * Get caching instance
     */
    public static function getCaching($idx=0)
    {
        //Get Ini Configuration
        My_Zend_Globals::getConfiguration();
        //Get caching instance
        if (is_null(self::$caching[$idx]))
        {        	
            self::$caching[$idx] = My_Zend_Cache::getInstance(self::$configuration->caching->$idx->toArray());            
        }

        //Return caching
        return self::$caching[$idx];
    }

    /**
     * Get configuration instance
     */
    public static function getConfiguration()
    {
        //Get Ini Configuration
        if (is_null(self::$configuration))
        {
            self::$configuration = Zend_Registry::get(APPLICATION_CONFIGURATION);
        }
        return self::$configuration;
    }

    /**
     * Get solr instance
     */
    public static function &getSolr($idex=5)
    {
        require_once( 'Apache/Solr/Service.php' );

        //Get Ini Configuration
        My_Zend_Globals::getConfiguration();

        //Get caching instance
        if (is_null(self::$solrList[$idex]))
        {
            self::$solrList[$idex] = new Apache_Solr_Service(self::$configuration->solr->$idex->host, self::$configuration->solr->$idex->port, self::$configuration->solr->$idex->admin);
        }

        //Solr instance
        $solrInstance = self::$solrList[$idex];

        //Put queue
        self::$arrSorl[] = $solrInstance;

        //Return caching
        return $solrInstance;
    }

    /**
     * Get storage instance
     * @param string $adapter
     */
    public static function &getStorage($adapter = 'mysql')
    {
        //Get Ini Configuration
        My_Zend_Globals::getConfiguration();

        //Get storage instance
        if (is_null(self::$storage))
        {
            switch (strtolower($adapter))
            {
                case 'mysql':
                //Set UTF-8 Collate and Connection
                    $options_storage = self::$configuration->storage->mysql->toArray();
                    $options_storage['params']['driver_options'] = array(
                            MYSQLI_INIT_COMMAND => 'SET NAMES utf8;'
                    );

                    //Create object to Connect DB
                    self::$storage = Zend_Db::factory($options_storage['adapter'], $options_storage['params']);

                    //Changing the Fetch Mode
                    self::$storage->setFetchMode(Zend_Db::FETCH_ASSOC);

                    //Create Adapter default is Db_Table
                    Zend_Db_Table::setDefaultAdapter(self::$storage);

                    // Set profiler
 					if(DEBUG_MODE == true)
 					{
                    	self::$storage->getProfiler()->setEnabled(true);
 					}
 					else 
 					{
 						self::$storage->getProfiler()->setEnabled(false);
 					}

                    //Unclean
                    unset($options_storage);
                    break;
                default:
                	//Set UTF-8 Collate and Connection
                	$options_storage = self::$configuration->storage->mysql->toArray();
                	$options_storage['params']['driver_options'] = array(
                			MYSQLI_INIT_COMMAND => 'SET NAMES utf8;'
                	);
                	
                	//Create object to Connect DB
                	self::$storage = Zend_Db::factory($options_storage['adapter'], $options_storage['params']);
                	
                	//Changing the Fetch Mode
                	self::$storage->setFetchMode(Zend_Db::FETCH_ASSOC);
                	
                	//Create Adapter default is Db_Table
                	Zend_Db_Table::setDefaultAdapter(self::$storage);
                	
                	// Set profiler
                	if(DEBUG_MODE == true)
                	{
                		self::$storage->getProfiler()->setEnabled(true);
                	}
                	else
                	{
                		self::$storage->getProfiler()->setEnabled(false);
                	}
                	
                	//Unclean
                	unset($options_storage);
                	
                    break;
            }
        }

        //Push to queue
        self::$arrStorage[] = self::$storage;

        //Return Db
        return self::$storage;
    }
  
    /**
     * Get mail instance
     */
    public static function &getMail()
    {
        //Get Ini Configuration
        My_Zend_Globals::getConfiguration();

        //Get storage instance
        if (is_null(self::$mail))
        {
            $smtp = self::$configuration->mail->smtp->toArray();
            $config = array('auth' => 'login',
                    'username' => $smtp['username'],
                    'password' => $smtp['password'],
                    'port' => $smtp['port']);

            $transport = new Zend_Mail_Transport_Smtp($smtp['host'], $config);
            Zend_Mail::setDefaultTransport($transport);

            //Create object mail
            self::$mail = new Zend_Mail();

            //Unset variable
            unset($transport);
            unset($config);
            unset($smtp);
        }

        //Push to queue
        self::$arrMail[] = self::$mail;

        //Return Mail
        return self::$mail;
    }

    /**
     * Close all connection
     */
    public function closeAllStorage()
    {
        foreach (self::$arrStorage as $storage)
        {
            //Try close
            if (!is_null($storage))
            {
                $storage->closeConnection();
            }
        }
        return true;
    }

    /**
     * Close all object mail
     */
    public function closeAllMail()
    {
        foreach (self::$arrMail as $mailInstance)
        {
            //Try close
            if (!is_null($mailInstance))
            {
                unset($mailInstance);
            }
        }
        return true;
    }

    /**
     * Dump logger
     * @param string $content
     */
    public static function dumpLogger($content, $br = false)
    {
        //Check content logger
        if (empty($content))
        {
            return false;
        }

        //Check instance
        if (!isset(self::$logger))
        {
            self::$logger = Zend_Registry::get(LOGGER_DUMP);
        }

        //Write dump
        self::$logger->log($content, $br);
    }

    /**
     * Send logger to scribe
     * @param int $meid
     * @param int $actionid
     * @param string $logdata
     */
    public static function scribeLogger($meid, $actionid, $logdata)
    {
        return My_Zend_Scribe::sendLog($meid, $actionid, $logdata);
    }

    /**
     * Gen key private
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public static function keyED($string, $keyphrase)
    {
        $keyphraseLength = strlen($keyphrase);
        $stringLength = strlen($string);
        for ($i = 0; $i < $stringLength; $i++)
        {
            $rPos = $i % $keyphraseLength;
            $r = ord($string[$i]) ^ ord($keyphrase[$rPos]);
            $string[$i] = chr($r);
        }
        return $string;
    }

    /**
     * encrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public static function encode($string, $keyphrase='HAeCwcaAhdm')
    {
        $string = self::keyED($string, $keyphrase);
        $string = base64_encode($string);
        return $string;
    }

    /**
     * decrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public static function decode($string, $keyphrase='HAeCwcaAhdm')
    {
        $string = base64_decode($string);
        $string = self::keyED($string, $keyphrase);
        return $string;
    }

    /**
     * Fetches an alternate IP address of the current visitor
     * attempting to detect proxies etc.
     */
    public static function getAltIp()
    {
        $alt_ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $alt_ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches))
        {
            // make sure we dont pick up an internal IP defined by RFC1918
            foreach ($matches[0] AS $ip)
            {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $ip))
                {
                    $alt_ip = $ip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_FROM']))
        {
            $alt_ip = $_SERVER['HTTP_FROM'];
        }

        return $alt_ip;
    }

    public static function verbalDate($day)
    {
    	if($day == 7)
    		$day = 'Chủ nhật ngày ';
    	else
    		$day = 'Thứ '.($day+1)." ngày ";
    	echo $day;
    }
    
    /**
     * Removes HTML characters and potentially unsafe scripting words from a string
     * @param string $message
     */
    public static function xssClean($message)
    {
        //If empty
        if (empty($message))
        {
            return '';
        }

        //Fix & but allow unicode
        $message = preg_replace('#&(?!\#[0-9]+;)#si', '', $message);
        $message = str_replace("<", "", $message);
        $message = str_replace(">", "", $message);
        //$message = str_replace("\"", "", $message);
        //Removes CSS
        static $preg_find = array('#javascript#i', '#vbscript#i');
        static $preg_replace = array('java script', 'vb script');
        return preg_replace($preg_find, $preg_replace, $message);
    }

    /**
     * Convert UTF8 to ASCII string
     * @param $tring
     * @param $bit
     * @return string
     */
    public static function utf8ToAscii($tring, $bit='-')
    {
        $utf8 = array(
                'ấ' => 'a',
                'ầ' => 'a',
                'ẩ' => 'a',
                'ẫ' => 'a',
                'ậ' => 'a',
                'Ấ' => 'a',
                'Ầ' => 'a',
                'Ẩ' => 'a',
                'Ẫ' => 'a',
                'Ậ' => 'a',
                'ắ' => 'a',
                'ằ' => 'a',
                'ẳ' => 'a',
                'ẵ' => 'a',
                'ặ' => 'a',
                'Ắ' => 'a',
                'Ằ' => 'a',
                'Ẳ' => 'a',
                'Ẵ' => 'a',
                'Ặ' => 'a',
                'á' => 'a',
                'à' => 'a',
                'ả' => 'a',
                'ã' => 'a',
                'ạ' => 'a',
                'â' => 'a',
                'ă' => 'a',
                'Á' => 'a',
                'À' => 'a',
                'Ả' => 'a',
                'Ã' => 'a',
                'Ạ' => 'a',
                'Â' => 'a',
                'Ă' => 'a',
                'ế' => 'e',
                'ề' => 'e',
                'ể' => 'e',
                'ễ' => 'e',
                'ệ' => 'e',
                'Ế' => 'e',
                'Ề' => 'e',
                'Ể' => 'e',
                'Ễ' => 'e',
                'Ệ' => 'e',
                'é' => 'e',
                'è' => 'e',
                'ẻ' => 'e',
                'ẽ' => 'e',
                'ẹ' => 'e',
                'ê' => 'e',
                'É' => 'e',
                'È' => 'e',
                'Ẻ' => 'e',
                'Ẽ' => 'e',
                'Ẹ' => 'e',
                'Ê' => 'e',
                'í' => 'i',
                'ì' => 'i',
                'ỉ' => 'i',
                'ĩ' => 'i',
                'ị' => 'i',
                'Í' => 'i',
                'Ì' => 'i',
                'Ỉ' => 'i',
                'Ĩ' => 'i',
                'Ị' => 'i',
                'ố' => 'o',
                'ồ' => 'o',
                'ổ' => 'o',
                'ỗ' => 'o',
                'ộ' => 'o',
                'Ố' => 'o',
                'Ồ' => 'o',
                'Ổ' => 'o',
                'Ô' => 'o',
                'Ộ' => 'o',
                'ớ' => 'o',
                'ờ' => 'o',
                'ở' => 'o',
                'ỡ' => 'o',
                'ợ' => 'o',
                'Ớ' => 'o',
                'Ờ' => 'o',
                'Ở' => 'o',
                'Ỡ' => 'o',
                'Ợ' => 'o',
                'ó' => 'o',
                'ò' => 'o',
                'ỏ' => 'o',
                'õ' => 'o',
                'ọ' => 'o',
                'ô' => 'o',
                'ơ' => 'o',
                'Ó' => 'o',
                'Ò' => 'o',
                'Ỏ' => 'o',
                'Õ' => 'o',
                'Ọ' => 'o',
                'Ô' => 'o',
                'Ơ' => 'o',
                'ứ' => 'u',
                'ừ' => 'u',
                'ử' => 'u',
                'ữ' => 'u',
                'ự' => 'u',
                'Ứ' => 'u',
                'Ừ' => 'u',
                'Ử' => 'u',
                'Ữ' => 'u',
                'Ự' => 'u',
                'ú' => 'u',
                'ù' => 'u',
                'ủ' => 'u',
                'ũ' => 'u',
                'ụ' => 'u',
                'ư' => 'u',
                'Ú' => 'u',
                'Ù' => 'u',
                'Ủ' => 'u',
                'Ũ' => 'u',
                'Ụ' => 'u',
                'Ư' => 'u',
                'ý' => 'y',
                'ỳ' => 'y',
                'ỷ' => 'y',
                'ỹ' => 'y',
                'ỵ' => 'y',
                'Ý' => 'y',
                'Ỳ' => 'y',
                'Ỷ' => 'y',
                'Ỹ' => 'y',
                'Ỵ' => 'y',
                'đ' => 'd',
                'Đ' => 'd',
                ' ' => $bit,
                '/' => $bit,
                '&' => $bit,
                '---' => $bit,
                '--' => $bit,
                '%20' => $bit
        );
        return str_replace(array_keys($utf8), array_values($utf8), $tring);
    }

    /**
     * Convert Iso88591 to Utf8 string
     * @param string $tring
     * @return string
     */
    public static function iso88591ToUtf8($tring)
    {
        $utf8 = array(
                'áº¥' => '&#7845;',
                'áº§' => '&#7847;',
                'áº©' => '&#7849;',
                'áº«' => '&#7851;',
                'áº­' => '&#7853;',
                'áº¤' => '&#7844;',
                'áº¦' => '&#7846;',
                'áº¨' => '&#7848;',
                'áºª' => '&#7850;',
                'áº¬' => '&#7852;',
                'áº¯' => '&#7855;',
                'áº±' => '&#7857;',
                'áº³' => '&#7859;',
                'áºµ' => '&#7861;',
                'áº·' => '&#7863;',
                'áº®' => '&#7854;',
                'áº°' => '&#7856;',
                'áº²' => '&#7858;',
                'áº´' => '&#7860;',
                'áº¶' => '&#7862;',
                'Ã¡' => '&aacute;',
                'Ã ' => '&agrave;',
                'áº£' => '&#7843;',
                'Ã£' => '&atilde;',
                'áº¡' => '&#7841;',
                'Ã¢' => '&acirc;',
                'Äƒ' => '&#259;',
                'Ã' => '&Aacute;',
                'Ã€' => '&Agrave;',
                'áº¢' => '&#7842;',
                'Ãƒ' => '&Atilde;',
                'áº ' => '&#7840;',
                'Ã‚' => '&Acirc;',
                'Ä‚' => '&#258;',
                'áº¿' => '&#7871;',
                'á»' => '&#7873;',
                'á»ƒ' => '&#7875;',
                'á»…' => '&#7877;',
                'á»‡' => '&#7879;',
                'áº¾' => '&#7870;',
                'á»€' => '&#7872;',
                'á»‚' => '&#7874;',
                'á»„' => '&#7876;',
                'á»†' => '&#7878;',
                'Ã©' => '&eacute;',
                'Ã¨' => '&egrave;',
                'áº»' => '&#7867;',
                'áº½' => '&#7869;',
                'áº¹' => '&#7865;',
                'Ãª' => '&ecirc;',
                'Ã‰' => '&Eacute;',
                'Ãˆ' => '&Egrave;',
                'áºº' => '&#7866;',
                'áº¼' => '&#7868;',
                'áº¸' => '&#7864;',
                'ÃŠ' => '&Ecirc;',
                'Ã­' => '&iacute;',
                'Ã¬' => '&igrave;',
                'á»‰' => '&#7881;',
                'Ä©' => '&#297;',
                'á»‹' => '&#7883;',
                'Ã' => '&Iacute;',
                'ÃŒ' => '&Igrave;',
                'á»ˆ' => '&#7880;',
                'Ä¨' => '&#296;',
                'á»Š' => '&#7882;',
                'á»‘' => '&#7889;',
                'á»“' => '&#7891;',
                'á»•' => '&#7893;',
                'á»—' => '&#7895;',
                'á»™' => '&#7897;',
                'á»' => '&#7888;',
                'á»’' => '&#7890;',
                'á»”' => '&#7892;',
                'Ã”' => '&Ocirc;',
                'á»˜' => '&#7896;',
                'á»›' => '&#7899;',
                'á»' => '&#7901;',
                'á»Ÿ' => '&#7903;',
                'á»¡' => '&#7905;',
                'á»£' => '&#7907;',
                'á»š' => '&#7898;',
                'á»œ' => '&#7900;',
                'á»ž' => '&#7902;',
                'á» ' => '&#7904;',
                'á»¢' => '&#7906;',
                'Ã³' => '&oacute;',
                'Ã²' => '&ograve;',
                'á»' => '&#7887;',
                'Ãµ' => '&otilde;',
                'á»' => '&#7885;',
                'Ã´' => '&ocirc;',
                'Æ¡' => '&#417;',
                'Ã“' => '&Oacute;',
                'Ã’' => '&Ograve;',
                'á»Ž' => '&#7886;',
                'Ã•' => '&Otilde;',
                'á»Œ' => '&#7884;',
                'Ã”' => '&Ocirc;',
                'Æ ' => '&#416;',
                'á»©' => '&#7913;',
                'á»«' => '&#7915;',
                'á»­' => '&#7917;',
                'á»¯' => '&#7919;',
                'á»±' => '&#7921;',
                'á»¨' => '&#7912;',
                'á»ª' => '&#7914;',
                'á»¬' => '&#7916;',
                'á»®' => '&#7918;',
                'á»°' => '&#7920;',
                'Ãº' => '&uacute;',
                'Ã¹' => '&ugrave;',
                'á»§' => '&#7911;',
                'Å©' => '&#361;',
                'á»¥' => '&#7909;',
                'Æ°' => '&#432;',
                'Ãš' => '&Uacute;',
                'Ã™' => '&Ugrave;',
                'á»¦' => '&#7910;',
                'Å¨' => '&#360;',
                'á»¤' => '&#7908;',
                'Æ¯' => '&#431;',
                'Ã½' => '&yacute;',
                'á»³' => '&#7923;',
                'á»·' => '&#7927;',
                'á»¹' => '&#7929;',
                'á»µ' => '&#7925;',
                'Ã' => '&Yacute;',
                'á»²' => '&#7922;',
                'á»¶' => '&#7926;',
                'á»¸' => '&#7928;',
                'á»´' => '&#7924;',
                'Ä‘' => '&#273;',
                'Ä' => '&#272;'
        );
        return str_replace(array_values($utf8), array_keys($utf8), $tring);
    }

    /**
     * Remove whitespace more in content
     * @param string $bff
     * @return string
     */
    public static function stripBuffer($bff)
    {
        /* carriage returns, new lines */
        $bff = str_replace(array("\r\r\r", "\r\r", "\r\n", "\n\r", "\n\n\n", "\n\n", "\n"), "", $bff);
		
        /* tabs */
        $bff = str_replace(array("\t\t\t", "\t\t", "\t\n", "\n\t", "\t"), "", $bff);

        /* opening HTML tags */
        $bff = str_replace(array(">\r<a", ">\r <a", ">\r\r <a", "> \r<a", ">\n<a", "> \n<a", "> \n<a", ">\n\n <a"), "><a", $bff);
        $bff = str_replace(array(">\r<b", ">\n<b"), "><b", $bff);
        $bff = str_replace(array(">\r<d", ">\n<d", "> \n<d", ">\n <d", ">\r <d", ">\n\n<d"), "><d", $bff);
        $bff = str_replace(array(">\r<f", ">\n<f", ">\n <f"), "><f", $bff);
        $bff = str_replace(array(">\r<h", ">\n<h", ">\t<h", "> \n\n<h"), "><h", $bff);
        $bff = str_replace(array(">\r<i", ">\n<i", ">\n <i"), "><i", $bff);
        $bff = str_replace(array(">\r<i", ">\n<i"), "><i", $bff);
        $bff = str_replace(array(">\r<l", "> \r<l", ">\n<l", "> \n<l", ">  \n<l", "/>\n<l", "/>\r<l"), "><l", $bff);
        $bff = str_replace(array(">\t<l", ">\t\t<l"), "><l", $bff);
        $bff = str_replace(array(">\r<m", ">\n<m"), "><m", $bff);
        $bff = str_replace(array(">\r<n", ">\n<n"), "><n", $bff);
        $bff = str_replace(array(">\r<p", ">\n<p", ">\n\n<p", "> \n<p", "> \n <p"), "><p", $bff);
        $bff = str_replace(array(">\r<s", ">\n<s"), "><s", $bff);
        $bff = str_replace(array(">\r<t", ">\n<t"), "><t", $bff);

        /* closing HTML tags */
        $bff = str_replace(array(">\r</a", ">\n</a"), "></a", $bff);
        $bff = str_replace(array(">\r</b", ">\n</b"), "></b", $bff);
        $bff = str_replace(array(">\r</u", ">\n</u"), "></u", $bff);
        $bff = str_replace(array(">\r</d", ">\n</d", ">\n </d"), "></d", $bff);
        $bff = str_replace(array(">\r</f", ">\n</f"), "></f", $bff);
        $bff = str_replace(array(">\r</l", ">\n</l"), "></l", $bff);
        $bff = str_replace(array(">\r</n", ">\n</n"), "></n", $bff);
        $bff = str_replace(array(">\r</p", ">\n</p"), "></p", $bff);
        $bff = str_replace(array(">\r</s", ">\n</s"), "></s", $bff);

        /* other */
        $bff = str_replace(array(">\r<!", ">\n<!"), "><!", $bff);
        $bff = str_replace(array("\n<div"), " <div", $bff);
        $bff = str_replace(array(">\r\r \r<"), "><", $bff);
        $bff = str_replace(array("> \n \n <"), "><", $bff);
        $bff = str_replace(array(">\r</h", ">\n</h"), "></h", $bff);
        $bff = str_replace(array("\r<u", "\n<u"), "<u", $bff);
        $bff = str_replace(array("/>\r", "/>\n", "/>\t"), "/>", $bff);
        $bff = preg_replace("# {2,}#", ' ', $bff);
        $bff = preg_replace("#  {3,}#", '  ', $bff);
        $bff = str_replace("> <", "><", $bff);
        $bff = str_replace("  <", "<", $bff);

        /* non-breaking spaces */
        $bff = str_replace(" &nbsp;", "&nbsp;", $bff);
        $bff = str_replace("&nbsp; ", "&nbsp;", $bff);

        return $bff;
    }

    /**
     * random number generator
     *
     * @param	integer	Minimum desired value
     * @param	integer	Maximum desired value
     * @param	mixed	Seed for the number generator
     * (if not specified, a new seed will be generated)
     */
    public static function randNumber($min, $max, $seed = -1)
    {
        if (!defined('RAND_SEEDED'))
        {
            if ($seed == -1)
            {
                $seed = (double) microtime() * 1000000;
            }
            mt_srand($seed);
            define('RAND_SEEDED', true);
        }
        return mt_rand($min, $max);
    }

    /**
     * Random an word
     * @param int $number
     * @return string
     */
    public static function randWord($number)
    {
        $arrChar = 'abRTUcdefghEFGHnr4678tuxMyzABCNvwPQVjkmWXYZ2pq3DJK9';
        $string = '';
        $arrCharLength = strlen($arrChar) - 1;
        for ($j = 0; $j < $number; $j++)
        {
            $string .= substr($arrChar, self::randNumber(0, $arrCharLength), 1);
        }
        return $string;
    }

    public static function formatDate($dateTime)
    {
    	if(empty($dateTime))
    		return "";
        $curTime = time();
        $listCurDate = array(date('H', $curTime), date('i', $curTime), date('d', $curTime), date('m', $curTime), date('Y', $curTime));
        $listTimeDate = array(date('H', $dateTime), date('i', $dateTime), date('d', $dateTime), date('m', $dateTime), date('Y', $dateTime));
        $limitTime = 24 * 60 * 60;
        $pastTime = $curTime - $dateTime;
        if ($pastTime <= $limitTime && ($listCurDate[2] == $listTimeDate[2]))
        {
            if ($pastTime > 0 && $pastTime <= 60)
            {
                $dateText = $pastTime . ' second ago';
            } elseif ($pastTime > 60 && $pastTime <= 3600)
            {
                $dateText = ceil(($pastTime / 60)) . ' minute ago';
            } elseif ($pastTime > 3600 && $pastTime <= 24 * 3600)
            {
                $dateText = ceil(($pastTime / (60 * 60))) . ' hour ago';
            } else
            {
                $dateText = '1 second ago';
            }
        } else
        {
            $dateText = self::getTextTime($listTimeDate) . ' ' . self::getTextDate($listCurDate, $listTimeDate);
        }

        return $dateText;
    }

    public static function timeToDate($mktime)
    {
    	$strTextual = array(
    				'Mon'	=> 'Thá»© hai',
    				'Tue'	=> 'Thá»© ba',
    				'Wed'	=> 'Thá»© tÆ°',
    				'Thu'	=> 'Thá»© nÄƒm',
    				'Fri'	=> 'Thá»© sÃ¡u',
    				'Sat'	=> 'Thá»© báº£y',
    				'Sun'	=> 'Chá»§ nháº­t'
    	);
    	
    	$str = date('d/m/Y', $mktime) . ' GMT+7';
    	
    	return $strTextual[date('D', $mktime)] .', '. $str;
    }
    
    public static function getTextTime($timeInfo)
    {
        $timeText = ($timeInfo[0] > 12) ? ($timeInfo[0] - 12) : $timeInfo[0];
        $timeText .= ':' . $timeInfo[1] . ' ';
        if ($timeInfo[0] >= 1 && $timeInfo[0] <= 10)
        {
            $timeText .= 'AM';
            if ($timeInfo[0] == 10 && $timeInfo[1] > 59)
            {
                $timeText .= 'AM';
            }
        } elseif ($timeInfo[0] >= 11 && $timeInfo[0] <= 13)
        {
            $timeText .= 'PM';
            if ($timeInfo[0] == 13 && $timeInfo[1] > 59)
            {
                $timeText .= 'PM';
            }
        } elseif ($timeInfo[0] >= 13 && $timeInfo[0] <= 18)
        {
            $timeText .= 'PM';
            if ($timeInfo[0] == 18 && $timeInfo[1] > 59)
            {
                $timeText .= 'PM';
            }
        } else
        {
            $timeText .= 'PM';
        }

        return $timeText;
    }

    public static function getTextDate($curInfo, $timeInfo)
    {
        $dateText = '';
        if ($curInfo[2] == $timeInfo[2])
        {
            if ($curInfo[3] == $timeInfo[3])
            {
                $dateText = 'today';
            } else
            {
                $dateText = $timeInfo[2] . '/' . $timeInfo[3] . '/' . $timeInfo[4];
            }
        } elseif (($curInfo[2] - $timeInfo[2]) == 1)
        {
            if ($curInfo[3] == $timeInfo[3])
            {
                $dateText = 'yesterday';
            } else
            {
                $dateText = $timeInfo[2] . '/' . $timeInfo[3] . '/' . $timeInfo[4];
            }
        } else
        {
            $dateText = $timeInfo[2] . '/' . $timeInfo[3] . '/' . $timeInfo[4];
        }

        return $dateText;
    }

    /**
     * cut UTF8 string
     *
     * @param string $str
     * @param int $len
     * @param string $charset
     * @return string
     */
    public static function cutString($str, $start, $len, $charset='UTF-8')
    {
        //Neu la chuoi rong
        if (empty($str))
        {
            return '';
        }

        //Kiem tra do dai chuoi
        if (iconv_strlen($str, $charset) <= $len)
        {
            return $str;
        }

        //Tien hanh cat chuoi
        $str = html_entity_decode($str, ENT_QUOTES, $charset);
        $str = iconv_substr($str, $start, $len, $charset);
        return $str . "...";
    }

    /**
     * Return emotion list
     * @return string
     */
    public static function getEmotionJsonString()
    {
        return json_encode(self::$arr_emotion);
    }

    /**
     * Return html emotion
     */
    public static function getEmotionHtml($length=39)
    {
        $img_smile_server = 'http://static.me.zing.vn/images/smilley/default/';
        $str = '<table width="450" border="0" cellpadding="0" cellspacing="0">';
        $str .= '<tbody>';
        $number = 0;
        $start = 0;
        $idex = 0;
        foreach (self::$arr_emotion as $key => $value)
        {
            if ($idex > $length)
            {
                break;
            }

            if ($number == 0)
            {
                $str .= '<tr>';
            }

            //Add td
            $str .= '<td><img title="' . $key . '" src="' . $img_smile_server . $value . '.jpg" onClick="emoticon_msg(this);" style="cursor: pointer;"></td>';
            $number++;

            if ($number == 10)
            {
                $number = 0;

                //Check start
                if ($start == 0)
                {
                    $str .= '<td rowspan="4" align="right" valign="middle"><img src="http://123mua.apps.zing.vn/c/images/emoticon_bar_close.gif" style="cursor: pointer;" onClick="emoticon_close()"></td>';
                }
                $start++;

                //Add Tr
                $str .= '</tr>';
            }
            $idex++;
        }

        //Loop number
        if ($number > 0)
        {
            for ($i = 0; $i < (10 - $number); $i++)
            {
                $str .= '<td>&nbsp;</td>';
            }
            $str .= '</tr>';
        }

        $str .= '</tbody>';
        $str .= '</table>';
        return $str;
    }

    /**
     * Strip word html
     * @param string $text
     */
    public static function strip_word_html($text, $allowed_tags = '<a><h1><h2><h3><style><p><br /><br><strong><i><u><span><b><center><dd><dt><font><img><ul><li><ol><pre><table><td><title><td><tr><tt><div><em>')
    {
        $text = strip_tags($text, $allowed_tags);
        
        $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
        		'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        		'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
        );
        
        $text = preg_replace($search, '', $text);
                
        return $text;
    }
    
    public static function removeNonUTF8($string)
    {
    	//reject overly long 2 byte sequences, as well as characters above U+10000 and replace with ?
		$string = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
		 '|[\x00-\x7F][\x80-\xBF]+'.
		 '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
		 '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
		 '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
		 '?', $string );
		
		//reject overly long 3 byte sequences and UTF-16 surrogates and replace with ?
		$string = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]'.
		 '|\xED[\xA0-\xBF][\x80-\xBF]/S','?', $string );
   		
		return $string;
    }

    /**
     * convert object to array
     *
     * @param object $obj
     * @return array|boolean
     */
    public static function objectToArray($obj)
    {
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
        foreach ($_arr as $key => $val)
        {
            $val = (is_array($val) || is_object($val)) ? self::objectToArray($val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }

    /**
     * Check is Ascii string
     * @param string $string
     */
    public static function isAsciiString($string)
    {
        return!(preg_match('/[^\x00-\x7F]/S', $string));
    }

    /**
     * Lay do dai cua chuoi
     * @param string $string
     * @return int
     */
    public static function vbstrlen($string)
    {
        $string = preg_replace('#&\#([0-9]+);#', '_', $string);
        return strlen($string);
    }

    /**
     * Loc du lieu
     * @param string $text
     * @param string $censorwords
     * @param string $censorchar
     * @param boolean $flag
     * @return string
     */
    public static function fetchCensoredText($text, $censorwords="{fuck},{shit},{ifastnet},{sitesled},{buá»“i},{Ä‘á»‹t},{Ä‘á»¥},{ná»©ng},{lá»“n},{fuck},{fucking},{cock},{bitch},{cáº·c},{cáº·t},{káº·c},{Äá»¥},{Ä‘Ã©o},{suck},{vcl},{bullshit},{minh rÃ¢u},{Ä‘á»¥ mÃ¡},{ÄÄ©},{viá»‡t cá»™ng},{NÃ´ng Thá»‹ XuÃ¢n},{triá»u Ä‘Ã¬nh cá»™ng sáº£n},{vÆ°Æ¡ng triá»u cá»™ng sáº£n},{vÆ°Æ¡ng triá»u HÃ  Ná»™i},{Ä‘áº£ng trá»‹},{Ä‘áº£ Ä‘áº£o cá»™ng sáº£n},{Ä‘áº£ Ä‘áº£o XHCN},{cá»™ng sáº£n bÃ¡n nÆ°á»›c},{tá»™i Ã¡c cá»™ng sáº£n},{tá»™i Ã¡c cá»§a cháº¿ Ä‘á»™ CSVN},{tá»™i Ã¡c cá»§a cháº¿ Ä‘á»™ cá»™ng sáº£n},{cá»™ng sáº£n Ä‘á»™c tÃ i},{Ä‘á»™c tÃ i cá»™ng sáº£n},{cá»™ng sáº£n thá»‘i nÃ¡t},{cá»™ng sáº£n má»‹ dÃ¢n},{cá»™ng sáº£n má»µ dÃ¢n},{Ä‘á»‹t máº¹ mÃ y},{Ä‘Ã©o máº¹},{Ä‘á»¥ máº¹},{lá»“n máº¹},{lá»— Ä‘Ã­t},{mÃ³c lá»“n},{liáº¿m lá»“n},{Äƒn lá»“n},{liáº¿m lÃ¬n},{máº·t lÃ¬n},{con phÃ²},{con káº·c},{Minh RÃ¢u},{viá»‡t minh},{viá»‡t cá»™ng},{Ä‘á»‹t},{Ä‘Ã©o},{lá»“n},{buá»“i},{cáº·c},{Ä‘á»‹t máº¹},{lá»‹t máº¹},{Ä‘á»¥ máº¡},{Ä‘Ã¹ mÃ¡},{Ä‘á»¥ máº¹},{Ä‘Ã©o máº¹},{Ä‘á»‹t máº¹ mÃ y},{Ä‘á»¥ máº¡ mÃ y},{Ä‘Ã¹ mÃ¡ mÃ y},{Ä‘á»¥ máº¹ mÃ y},{Ä‘Ã©o máº¹ mÃ y},{Ä‘á»‹t máº¹ chÃºng mÃ y},{Ä‘á»¥ máº¡ chÃºng mÃ y},{Ä‘Ã¹ mÃ¡ chÃºng mÃ y},{Ä‘á»¥ máº¹ chÃºng mÃ y},{Ä‘Ã©o máº¹ chÃºng mÃ y},{lá»“n máº¹ mÃ y},{tiÃªn sÆ° bá»‘},{tá»• sÆ° bá»‘},{máº£ máº¹ mÃ y},{máº£ cha mÃ y},{máº£ bá»‘ mÃ y},{máº£ tá»• mÃ y},{máº£ máº¹ chÃºng mÃ y},{máº£ cha chÃºng mÃ y},{máº£ bá»‘ chÃºng mÃ y},{máº£ tá»• chÃºng mÃ y},{lá»— Ä‘Ã­t},{máº·t lá»“n},{liáº¿m lá»“n},{mÃ³c lá»“n},{Äƒn lá»“n},{máº·t lÃ¬n},{liáº¿m lÃ¬n},{mÃ³c lÃ¬n},{Äƒn lÃ¬n},{liáº¿m Ä‘Ã­t},{con phÃ²},{con Ä‘Ä©},{con Ä‘iáº¿m},{Ä‘Ä© Ä‘á»±c},{Ä‘iáº¿m Ä‘á»±c},{Ä‘á»“ chÃ³},{Ä‘á»“ lá»£n},{liáº¿m cá»©t},{bá»‘c cá»©t},{Äƒn cá»©t},{nhÃ©t cá»©t},{Äƒn lá»“n},{mÃ¡u lá»“n},{Äƒn buá»“i},{mÃºt buá»“i},{Äƒn cáº·c},{bÃº cáº·c},{con cáº·c},{mÃºt cáº·c},{Äƒn káº·c},{bÃº káº·c},{cá»§ káº·c},{con káº·c},{mÃºt káº·c},{Äƒn káº¹c},{bÃº káº¹c},{cá»§ káº¹c},{con káº¹c},{bÃº dÃ¡i},{bÃº dzÃ¡i},{mÃºt zÃ¡i},{bÃº buá»“i},{Ä‘áº§u buá»“i},{bÃº cu},{bÃº ku},{con ku},{Äá»ŠT Máº¸},{DIT ME},{Äy~ chÃ³},{ÄÄ© chÃ³},{Ä‚N Ká»¨T},{Äƒn ká»©t},{ngá»©a dzÃ¡i},{bÃ¡n dzÃ¢m},{ngá»©a zdÃ¡i},{cá» ba sá»c},{cá» 3 sá»c},{nguoi-viet.com},{bá» máº¹},{MINH RÃ‚U},{Lá»’N},{Cá»¨T},{Äá»´TÄá»ŠT},{ÄÄ¨},{Äá»ˆ},{Ä‘Ä©},{Ä‘á»‰},{Ä‘Ä©},{loz`},{Ä‘Ä©?},{Ä‘Ä¨},{Äá»‹t máº¹},{Äá»‹t},{Ä‘á»µt máº¹},{Ä‘á»µt},{Äá»µt máº¹},{Äá»µt},{Ä‘á»´t},{ná»©ng cáº¹c},{ná»©ng káº¹t},{dis máº¹},{Ä‘is máº¹},{Ä‘Is máº¹},{Ä‘á»¤ máº¸},{nhu lon},{lon`},{nhÆ° lá»“n},{phim sex},{film sex},{djs},{zÃº bá»±},{zu' bu},{zu' bá»±},{khoe hang`},{KHOE HÃ€NG},{KHOE HANG`},{Minh Trá»‹},{Minh trá»‹},{minh trá»‹},{MINH TRá»Š},{MINH TRI},{minh tri},{Cá»œ VÃ€NG BA Sá»ŒC Äá»Ž},{Cá» VÃ ng Ba Sá»c Äá»},{cá» vÃ ng ba sá»c Ä‘á»},{Cá»œ VÃ€NG 3 Sá»ŒC Äá»Ž},{Cá» VÃ ng 3 Sá»c Äá»},{cá» vÃ ng ba sá»c Ä‘á»},{Ä‘Ã n Ã¡p tÃ´n giÃ¡o},{ÄÃ€N ÃP TÃ”N GIÃO},{ÄÃ n Ãp TÃ´n GiÃ¡o},{ThÄƒng Tiáº¿n Viá»‡t Nam},{THÄ‚NG TIáº¾N VIá»†T NAM},{thÄƒng tiáº¿n viá»‡t nam},{thÄƒng tiáº¿n Viá»‡t Nam},{Cá»¥ Há»“},{Cu Há»“},{Ä‘Ã©o},{Äá»¤},{Ä‘á»¥},{Äá»ŠT},{Ä‘á»‹t tá»•},{ná»©ng},{lá»“n},{máº¥t dáº¡y},{Ä‘á»‹t},{ngá»©a},{ná»©ng lá»“n},{Ä‘Ã©o},{dis},{hot^. le.},{há»™t le},{cleversky.net},{Äjt},{lá»’n},{Ä‘Ä©},{Lá»’N},{deo'},{liem^'},{cho'},{cut'},{hiep'},{trYm},{Ä‘Ä©},{Cá»™ng sáº£n Quá»‘c táº¿},{Viá»‡t Nam Cá»™ng HÃ²a},{VN CH},{CS VN},{Trung cá»™ng},{ViÃªt Cá»™ng},{TRung Cong},{Viet COng},{TRung COng},{Thanh Minh Thiá»n viá»‡n},{Ä‘ Ä©},{máº·t Lá»’N},{chÃ³},{Äá»´T},{trYm},{á»ˆA},{Äá»µt máº¸},{BUá»’I},{hÃ£m},{Cá»©t},{Cá»¨T},{cá»©t},{cá»¨t},{cá»©T},{Cá»¨t},{CPVNTD},{sjp},{ÄÃT},{BUá»’I},{Äá»¥},{Äá»ŠT},{LO^`N},{BUÃ”`i},{d!ck},{Ä‘á»¤},{Ä‘ Ä©},{trYm},{ná»©ng},{cáº·c},{lá»“n},{Ä‘á»‹t},{Ä‘á»ŠT},{lÃ’l},{Ä‘Ã­t},{lá»’n},{Äá»´T},{Äá»´T mje},{Äá»µt},{Äá»µt máº¸Ä‘ Ä©},{á»ˆA},{Cá»¨T},{cá»©t},{Ä‘Ã©o},{chÃ³ Ä‘áº»},{FUCK},{loZ},{Ä‘á»ŠT},{lÃ’l},{máº¹ mÃ y},{mÃ¡ mÃ y},{Máº¤T Dáº Y},{máº¥t dáº¡y},{f.u.c.k},{F.U.C.K},{f u c k},{MÃ MÃ€Y},{Máº¸ MÃ€Y},{Fuck},{FucK},{F U C K},{Ä‘Ãº},{Ä‘á»‰},{Ä‘Ä©},{Ä‘á»‰},{vÃº},{dÃº},{Ä‘Ã¡i},{dY~},{diE^n},{cH0},{cH0'},{mE.},{zÃº},{fáº£n Ä‘á»™ng},{pháº£n Ä‘á»™ng},{djs},{ch.Ã³},{F*ck},{Ä‘.Ã©.o},{Ä‘Ã­t gháº»},{Ä‘.e'o}", $censorchar='*', $flag= 1)
    {
        static $arrcensorwords;
        if (empty($text))
        {
            return "";
        }
        if ($flag == true AND !empty($censorwords))
        {
            if (empty($arrcensorwords))
            {
                $censorwords = preg_quote($censorwords, '#');
                $arrcensorwords = preg_split('#[,\r\n\t]+#', $censorwords, -1, PREG_SPLIT_NO_EMPTY);
            }

            foreach ($arrcensorwords AS $arrcensorword)
            {
                if (substr($arrcensorword, 0, 2) == '\\{')
                {
                    if (substr($arrcensorword, -2, 2) == '\\}')
                    {
                        // prevents errors from the replace if the { and } are mismatched
                        $arrcensorword = substr($arrcensorword, 2, -2);
                    }
                    // ASCII character search 0-47, 58-64, 91-96, 123-127
                    $nonword_chars = '\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f';
                    // words are delimited by ASCII characters outside of A-Z, a-z and 0-9
                    $text = preg_replace(
                            '#(?<=[' . $nonword_chars . ']|^)' . $arrcensorword . '(?=[' . $nonword_chars . ']|$)#si',
                            str_repeat($censorchar, self::vbstrlen($arrcensorword)),
                            $text
                    );
                } else
                {
                    $text = preg_replace("#$arrcensorword#si", str_repeat($censorchar, self::vbstrlen($arrcensorword)), $text);
                }
            }
        }
        return $text;
    }

    /**
     * Attempts to intelligently wrap excessively long strings onto multiple lines
     *
     * @param	string	Text to be wrapped
     * @param	integer	If specified, max word wrap length
     * @param	string	Text to insert at the wrap point
     *
     * @return	string
     */
    public function fetch_word_wrapped_string($text, $limit = 20, $wraptext = ' ')
    {
        $limit = intval($limit);

        if ($limit > 0 AND !empty($text))
        {
            return preg_replace('
				#((?>[^\s&/<>"\\-\[\]]|&[\#a-z0-9]{1,7};){' . $limit . '})(?=[^\s&/<>"\\-\[\]]|&[\#a-z0-9]{1,7};)#i',
                    '$0' . $wraptext,
                    $text
            );
        } else
        {
            return $text;
        }
    }

    /**
     * get partition global
     *
     * @param array $arr
     * @param int $id
     * @return string
     */
    private static function getPartitionDb($userid, $maxlength)
    {
        //Neu maxlenght = 0
        if ($maxlength == 0)
        {
            return '';
        }

        //Check userid
        $userid = (int) ($userid % 5000000);

        //Lay phan div
        $div = intval($userid / $maxlength);

        //Lay thong tin table
        return ($div * $maxlength + 1) . '_' . ($div + 1) * $maxlength;
    }

    public static function distanceOfTimeInWords($fromTime)
    {
        $distanceInSeconds = round(abs(time() - $fromTime));
        $distanceInMinutes = round($distanceInSeconds / 60);

        if ($distanceInMinutes <= 1)
        {
            return 'má»›i tá»©c thÃ¬';
        }
        if ($distanceInMinutes <= 2)
        {
            return '1 phÃºt trÆ°á»›c';
        }
        if ($distanceInMinutes < 45)
        {
            return $distanceInMinutes . ' phÃºt trÆ°á»›c';
        }
        if ($distanceInMinutes < 90)
        {
            return 'cÃ¡ch Ä‘Ã¢y 1 giá»';
        }
        if ($distanceInMinutes < 1440)
        {
            return 'cÃ¡ch Ä‘Ã¢y ' . round(floatval($distanceInMinutes) / 60.0) . ' giá»';
        }
        if ($distanceInMinutes < 2880)
        {
            return '1 ngÃ y';
        }
        if ($distanceInMinutes < 43200)
        {
            return round(floatval($distanceInMinutes) / 1440) . ' ngÃ y trÆ°á»›c';
        }
        if ($distanceInMinutes < 86400)
        {
            return '1 thÃ¡ng trÆ°á»›c';
        }
        if ($distanceInMinutes < 525600)
        {
            return round(floatval($distanceInMinutes) / 43200) . ' thÃ¡ng';
        }
        if ($distanceInMinutes < 1051199)
        {
            return '1 nÄƒm trÆ°á»›c';
        }

        return 'hÆ¡n ' . round(floatval($distanceInMinutes) / 525600) . ' nÄƒm';
    }

    public static function mktimeToDate($mktime)
    {
        return date("Y-m-d G:i:s", $mktime);
    }

    /**
     * Ham tinh thoi gian giua 2 thoi diem, tra ve so giay
     *
     * @param string $time1
     * @param string $time2
     * @return int
     */
    public static function dateDiff($startDate, $endDate)
    {
    	$startDate = strtotime($startDate);
    	$endDate = strtotime($endDate);
    	
    	if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate)
    		return false;
    	 
    	$years = date('Y', $endDate) - date('Y', $startDate);
      
    	$endMonth = date('m', $endDate);
    	$startMonth = date('m', $startDate);
      
    	// Calculate months
    	$months = $endMonth - $startMonth;
    	if ($months <= 0)  {
    		$months += 12;
    		$years--;
    	}
    	if ($years < 0)
    		return false;
      
    	// Calculate the days
    	$offsets = array();
    	if ($years > 0)
    		$offsets[] = $years . (($years == 1) ? ' year' : ' years');
    	if ($months > 0)
    		$offsets[] = $months . (($months == 1) ? ' month' : ' months');
    	$offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';

    	$days = $endDate - strtotime($offsets, $startDate);
    	$days = date('z', $days);
    	 
    	return array($years, $months, $days);
    }

    public static function getStringReplaceEmotion($string)
    {
        //Neu la chuoi rong
        if (empty($string))
        {
            return '';
        }

        //$img_smile_server = 'http://static.me.zing.vn/images/smilley/default/';
        $img_smile_server = 'http://img.me.zdn.vn/images/smilley/default/';

        foreach (self::$arr_emotion as $key => $value)
        {
            $str_replace = '<img title="" src="' . $img_smile_server . $value . '.jpg" align="absmiddle"/>&nbsp;';
            $string = str_replace($key, $str_replace, $string);
        }

        return $string;
    }

    public static function escape($string)
    {
        $match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' ');
        $replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ ');
        $string = str_replace($match, $replace, $string);
        return $string;
    }

    /**
     *
     * Get thumb image
     * @param string $url
     * @param int $width
     * @param int $height
     */
	public static function getThumbImage($url, $type='')
    {
    	// get extension
    	$ext = explode('.', $url);
    	$ext = $ext[count($ext) - 1];    	
    	
    	$type = strtolower($type);
    	
    	switch ($type){    		
    		case 'thumb':
    			$width = 146;
    			$height = 108;
    			break;		
    		default:
    			$width = 146;
    			$height = 108;
    			break;    			
    	}
    	
        $url = str_replace('.'. $ext, '_' . $width . 'x' . $height .'.'. $ext, $url);
        
        return $url;
    }

    public static function uploadFile($filename)
    {
        if (!file_exists($filename))
        {
            return false;
        }

        $params = array(
                'Filedata' => "@" . $filename,
        );

        //Build url
        $api_url = self::_genUploadUrl();

        //Post curl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);

        if(in_array(APPLICATION_ENVIRONMENT, array('production', 'staging')))
		{
			curl_setopt($curl, CURLOPT_PROXY, "10.30.15.68:8888");
			curl_setopt($curl, CURLOPT_PROXYPORT, 81);	
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,30);
		} 
		
        //Get respone
        $response = curl_exec($curl);

        //Check response
        if (empty($response))
        {
            return array();
        }

        $response = str_replace('<script type="text/javascript">document.domain = "zing.vn";</script>', '', $response);

        //Close curl
        curl_close($curl);

        return json_decode($response, true);
    }

    function download($file_source, $file_target)
    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $file_source);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		
        if(in_array(APPLICATION_ENVIRONMENT, array('production', 'staging')))
		{
			curl_setopt($curl, CURLOPT_PROXY, "10.30.15.68:8888");
			curl_setopt($curl, CURLOPT_PROXYPORT, 81);	
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,30);
		} 
		
        $data = curl_exec($curl);
        $rsInfo = curl_getinfo($curl);
        curl_close($curl);

        if (!$data || $rsInfo['http_code'] == 404)
        {
            return false;
        }
        file_put_contents($file_target, $data);

        return true;
    }

    private static function _genUploadUrl()
    {
        $appTime = time();
        $appToken = md5(QUICK_UPLOAD_API_ID . ":" . QUICK_UPLOAD_API_KEY . ":" . $appTime);
        return 'http://quick-upload.apps.zing.vn/api/rest?method=upload&app_id=' . QUICK_UPLOAD_API_ID . "&app_time=" . $appTime . "&app_token=" . $appToken;
    }

    /**
     *
     * URL Alias Creator
     * @param String $string
     */
    public static function aliasCreator($string)
    {
        //remove any '-' from the string they will be used as concatonater
        $string = str_replace('-', ' ', trim($string));
        $string = self::transliterate($string);
        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        //$string = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $string);
        $string = trim(preg_replace(array('/[^A-Za-z0-9\-]/'), array(' '), $string));
        $string = preg_replace('/\s+/', ' ', $string);
        $string = preg_replace('/\s+/', '-', $string);
        // lowercase and trim
        $string = trim(strtolower($string));
        return $string;
    }

    /**
     * Clean UTF-8 Charactor
     * @param String $string
     */
    private static function transliterate($string)
    {
        $string = trim(htmlentities(self::utf8ToAscii($string, " ")));
        $string = preg_replace(
                array('/&szlig;/', '/&(..)lig;/', '/&([aouAOU])uml;/', '/&(.)[^;]*;/'),
                array('ss', "$1", "$1" . 'e', "$1"),
                $string);

        return $string;
    }

    private static $_viewInstance;

    private static function _getViewInstance()
    {
        if (self::$_viewInstance == null)
        {
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            if (null === $viewRenderer->view)
            {
                $viewRenderer->initView();
            }
            self::$_viewInstance = $viewRenderer->view;
        }
        return self::$_viewInstance;
    }

    public static function setTitle($title)
    {
        $view = self::_getViewInstance();
        $view->headTitle()->setSeparator(' - ');
        $view->headTitle()->set($title);
    }

	public static function setMeta($name, $content, $split = false)
    {
    	$view = self::_getViewInstance();

    	if($split)
    	{
    		if($name == 'keywords')
    		{
    			$content = preg_replace('/[\s]+/', ', ', trim($content));
    		}
    	}

    	$view->headMeta()->setName($name, $content);
    }

    public static function setProperty($name, $content)
    {
    	self::setDocType('XHTML1_RDFA');
    	$view = self::_getViewInstance();
    	$view->headMeta()->setProperty($name, $content);
    }
    
    public static function setDocType($doctype)
    {
        $view = self::_getViewInstance();
        $view->doctype($doctype);
    }

    public static function sizeOfVar($var) {
	    $start_memory = memory_get_usage();
	    $tmp = $var;
	    return memory_get_usage() - $start_memory;
	}
       
    public static function filterTitle($title)
    {
    	$title = self::utf8ToAscii($title, ' ');
    	$title = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
    	
    	return $title;
    }
    
    public static function generatePhotoSignKey($time, $width=0, $height=0, $maxSize=0)
    {
    	$config = self::getConfiguration();
    
    	return md5($config->api->photo->seckey . $time . $width . $height . $maxSize);
    }
    
    public static function leechImage($img, $resize=0)
    {
    	try {
    		$img = str_replace(' ', '%20', $img);
    		$img = str_replace('(', '%28', $img);
    		$img = str_replace(')', '%29', $img);
    			
    		
    		//Get configuration
			$configuration = self::getConfiguration();
			$api_photo = $configuration->api->photo->baseurl;
			
			$params = array(
					'url' => $img,
					'resize' => $resize
			);
			
			//Build url
			$api_url = $api_photo . '/index/download';
		
			//Post curl
			$curl = curl_init();
			
			curl_setopt($curl, CURLOPT_URL, $api_url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 60);
			curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
			curl_setopt($curl, CURLOPT_VERBOSE, 0);
	
			//Get respone
			$response = curl_exec($curl);
			
			//Check response
			if (empty($response))
			{
				return array();
			}			
			
			//Close curl
			curl_close($curl);
    			
			$rs = json_decode($response, true);
			
    		return $rs;
    	}
    	catch(Exception $ex)
    	{
    		My_Zend_Logger::log('My_Zend_Globals::leechImage - '. $ex->getMessage());
    		return false;
    	}
    }
    
    function replace_br($data) {    
    	$data = preg_replace('#<p>(\s|&nbsp;|</?\s?br\s?/?>)*</?p>#', '', $data);
    	$data = preg_replace('#(?:<br\s*/?>\s*?){2,}#', '</p><p>', $data);    	
    	$data = preg_replace('#<br\s*/?>#', '</p><p>', $data);
    	$data = '<p>'. $data .'</p>';    	    	    	
    	//$data = preg_replace("/<[^\/>]*>([\s]?)*<\/[^>]*>/", '', $data);    	
    	return $data;
    }
    
    public static function isValidEmail($email) {
    	return filter_var($email, FILTER_VALIDATE_EMAIL)
    	&& preg_match('/@.+\./', $email);
    }
    
    public static function generateBreadCrumbs($controller, $description, $action, $linkOne, $linkTwo, $children)
    {
    	$html = '';
    	$html .='<h1>'.$controller.'<small>'.$description.'</small> </h1>';
    	$html .='<ol class="breadcrumb"><li><i class="fa fa-dashboard"></i> <a href="'.BASE_URL.'/adm">Trang chủ</a></li>';
    	$html .='<li><a href="'.$linkOne.'">'.$controller.'</a><span class="icon-angle-right"></span></li>';
    	if(!empty($children))
    	{
    		$html .= '<li><a href="'.$linkTwo.'">'.$action.'</a><span class="icon-angle-right"></span><li>'.$children.'</li>';
    	}else {
    		$html .= '<li class="active">'.$action.'</li>';
    	}
    	$html .='</ol>';
    	return $html;
    }
    
    public static function myArrayFlip($array, $key) 
    {
    	$arrayReturn = array();
    	if(!empty($array))
    	{
    		foreach($array as $val)
    		{
    			if(isset($val[$key]))
    			{
    				$arrayReturn[$val[$key]] = $val;
    			}
    		}
    	}
    	return $arrayReturn;
    }
    
    public static function numberFormat($number, $thousands_sep = '.')
    {
    	return number_format($number, 0, '.', $thousands_sep);
    }
    
    public static function getLabelSale($specialPrice, $productPrice){
    	$labelSale = 0;
    	if($specialPrice > 0){
    		$labelSale = round((($productPrice - $specialPrice)/ $productPrice) * 100);
    	}
    	return $labelSale;
    }
    
    public static function getDayNameVietNamese($date)
    {
    	$day = date('N', $date);
    	$day++;
    	if($day == 8)
    		$day= 'Chủ nhật';
    	else 
    		$day = 'Thứ '.$day;
    	
    	return $day;
    }
    
    public static function build_query_string($data, $fieldData = array(), $removeKey='')
    {
    	if(!empty($fieldData))
    	{
    		foreach($fieldData as $key => $value)
    		{
    			unset($data[$key]);
    		}
    	}
    
    	// merge data
    	if(!empty($fieldData) && is_array($fieldData))
    	{
    		$data = array_merge($data, $fieldData);
    	}
    
    	// remove key require
    	if(is_array($removeKey))
    	{
    		foreach($removeKey as $key)
    		{
    			unset($data[$key]);
    		}
    	}
    	elseif($removeKey != '')
    	{
    		unset($data[$removeKey]);
    	}
    
    	if(empty($data))
    	{
    		return '';
    	}
    
    	return '?'. http_build_query($data);
    }
}

