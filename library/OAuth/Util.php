<?php
/**
 * OAuth_Util
 * This class has bean auto_generated at 04/05/2011
 * @author
 */
class OAuth_Util 
{
	const CACHE_INFO_USER = 'info_user_';
	const LIMIT_TIME_REQUEST = 60;
	
	private static $_facebook;
	
	/**
	 * Get info UserFacebook
	 */
	public static function getInfoUserFacebook()
	{
		$facebook = self::getFacebookClient();
        $profile = $facebook->getUser();

        if ( $profile )
        {
            try
            {
                $userProfile = $facebook->api('/me');
                if ( !isset($userProfile['email']) )
                {
                    $userProfile['email'] = $userProfile['username'] . "@facebook.com";
                }
                
                return $userProfile;
            }
            catch (FacebookApiException $e)
            {
                error_log($e);
                $profile = null;
            }
        }
        return $profile;
	}
	
	/**
	 *
	 * @return Facebook
	 */
	public static function getFacebookClient()
	{
		if ( !self::$_facebook )
		{
			include_once LIBS_PATH . '/OAuth/Facebook/common.inc.php';
			self::$_facebook = new Facebook(array(
				'appId' => FACEBOOK_APP_KEY,
				'secret' => FACEBOOK_APP_SECKEY,
			));
		}
		return self::$_facebook;
	}
	
}

?>
