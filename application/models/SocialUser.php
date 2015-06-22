<?php

class SocialUser
{
    const _DB_INSTANCE = 1;
    const _CACHE_INSTANCE = 0;
    const _TABLE = 'social_users';
    const _prefix_social_user_id = "social_userid.";
    const TYPE_FACEBOOK = 'FB';
    const TYPE_YAHOO = 'YH';
    const TYPE_GOOGLE = 'GG';

    public static function insert($data)
    {
        $preparedData = self::_prepareData($data);
        $storage = self::_getDB();
        try
        {	
            $storage->insert(self::_TABLE, $preparedData);          
        }
        catch (Exception $e)
        {
            self::_errorLog('insert', $e);
            return 0;
        }
    }
    
    public static function getBySocialId($socialId, $type = self::TYPE_FACEBOOK)
    {     
    	
		$storage = My_Zend_Globals::getStorage();
		
        $select = $storage->select()
                          ->from(self::_TABLE)
                          ->where("user_social_id = '$socialId'")
                          ->where("type = '$type'");

        $data = $storage->fetchRow($select);
        
        return $data;
    }
    
    public static function _prepareData($data)
    {
        $cols = self::_getCols();
        $preparedData = array();
        foreach ($cols as $col)
        {
            if ( isset($data[$col]) )
            {
                $preparedData[$col] = $data[$col];
            }
        }
        return $preparedData;
    }

    public static function _getCols()
    {
        return array(
            'id', 'user_id', 'user_social_id', 'type'
        );
    }

    public static function _getDB()
    {
        return My_Zend_Globals::getStorage();
    }
   	
    private static function _errorLog($functionName, $e)
    {
        My_Zend_Logger::log("SocialUser::$functionName - {$e->getMessage()}");
    }

}

?>
