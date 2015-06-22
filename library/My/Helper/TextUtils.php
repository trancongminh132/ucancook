<?php

class My_Helper_TextUtils {
	
	/**
	 * Filter out bad words from text 
	 */
	public static function filter_bad_words($text){
		$cached_bad_words = self::get_bad_word_array();
		if(!empty($cached_bad_words)){
			foreach ($cached_bad_words as $key=>$value){
				$pattern = '/' . $value["word"] . '(?![a-zA-Z]+)/iu';
				$text = preg_replace($pattern,$value["replace"],$text);
			}
		}
		return $text;
	}
	
	/**
	 * Get all bad words and replace from bad_words file
	 *
	 * @return unknown
	 */
	public static function get_bad_word_array(){
		$Cache = My_Zend_Globals::getCaching();
		$bad_words = $Cache->read('123mua_bad_word_array');
		if(false == $bad_words){
			$path_file = LIBS_PATH . '/My/Helper/bad_words.txt';
			if (! file_exists ($path_file)) {
				echo $path_file . ' does not exist.';
				return false;
			}
			$handle = fopen ( $path_file, "r" );
			while (!feof($handle)) {
				$line = fgets($handle, 4096);
				$pos = self::utf8_strpos($line, "=");
				if($pos===false)
					continue;
				$bad_word = self::utf8_trim(self::utf8_substr($line, 0, $pos));
				$replacement = self::utf8_trim(self::utf8_substr($line, $pos+1));
				$bad_words[] = array("word"=>$bad_word,"replace"=>$replacement);
		    }
		    fclose($handle);
		    $Cache->write('123mua_bad_word_array', $bad_words);
		 }
		 return $bad_words;
	}
	
	/**
	 * UTF-8 aware replacement for ltrim()
	 * Note: you only need to use this if you are supplying the charlist
	 * optional arg and it contains UTF-8 characters. Otherwise ltrim will
	 * work normally on a UTF-8 string
	 * 
	 * @return string
	 */
	public static function utf8_ltrim( $str, $charlist = FALSE ) {
	    if($charlist === FALSE) return ltrim($str);
	    
	    //quote charlist for use in a characterclass
	    $charlist = preg_replace('!([\\\\\\-\\]\\[/^])!','\\\${1}',$charlist);
	    
	    return preg_replace('/^['.$charlist.']+/u','',$str);
	}
	
	/**
	 * UTF-8 aware replacement for rtrim()
	 * Note: you only need to use this if you are supplying the charlist
	 * optional arg and it contains UTF-8 characters. Otherwise rtrim will
	 * work normally on a UTF-8 string
	 * 
	 * @return string
	 */
	public static function utf8_rtrim( $str, $charlist = FALSE ) {
	    if($charlist === FALSE) return rtrim($str);
	    
	    //quote charlist for use in a characterclass
	    $charlist = preg_replace('!([\\\\\\-\\]\\[/^])!','\\\${1}',$charlist);
	  
	    return preg_replace('/['.$charlist.']+$/u','',$str);
	}
	
	/**
	 * UTF-8 aware replacement for trim()
	 * Note: you only need to use this if you are supplying the charlist
	 * optional arg and it contains UTF-8 characters. Otherwise trim will
	 * work normally on a UTF-8 string
	 * 
	 * @return string
	 */
	public static function utf8_trim( $str, $charlist = FALSE ) {
	    if($charlist === FALSE) return trim($str);
	    return self::utf8_ltrim(self::utf8_rtrim($str, $charlist), $charlist);
	}
	
	/**
	 * Wrapper round mb_strlen
	 * Assumes you have mb_internal_encoding to UTF-8 already
	 * Note: this function does not count bad bytes in the string - these
	 * are simply ignored
	 * @param string UTF-8 string
	 * 
	 * @return int number of UTF-8 characters in string
	 */
	public static function utf8_strlen($str){
	    return mb_strlen($str);
	}
	
	/**
	 * Assumes mbstring internal encoding is set to UTF-8
	 * Wrapper around mb_strpos
	 * Find position of first occurrence of a string
	 * @param string haystack
	 * @param string needle (you should validate this with utf8_is_valid)
	 * @param integer offset in characters (from left)
	 * 
	 * @return mixed integer position or FALSE on failure
	 */
	public static function utf8_strpos($str, $search, $offset = FALSE){
	    if ( $offset === FALSE ) {
	        return mb_strpos($str, $search);
	    } else {
	        return mb_strpos($str, $search, $offset);
	    }
	}
	
	/**
	 * Assumes mbstring internal encoding is set to UTF-8
	 * Wrapper around mb_strrpos
	 * Find position of last occurrence of a char in a string
	 * @param string haystack
	 * @param string needle (you should validate this with utf8_is_valid)
	 * @param integer (optional) offset (from left)
	 * 
	 * @return mixed integer position or FALSE on failure
	 */
	public static function utf8_strrpos($str, $search, $offset = FALSE){
	    if ( $offset === FALSE ) {
	        # Emulate behaviour of strrpos rather than raising warning
	        if ( empty($str) ) {
	            return FALSE;
	        }
	        return mb_strrpos($str, $search);
	    } else {
	        if ( !is_int($offset) ) {
	            trigger_error('utf8_strrpos expects parameter 3 to be long',E_USER_WARNING);
	            return FALSE;
	        }
	        
	        $str = mb_substr($str, $offset);
	        
	        if ( FALSE !== ( $pos = mb_strrpos($str, $search) ) ) {
	            return $pos + $offset;
	        }
	        
	        return FALSE;
	    }
	}
	
	/**
	 * Assumes mbstring internal encoding is set to UTF-8
	 * Wrapper around mb_substr
	 * Return part of a string given character offset (and optionally length)
	 * @param string
	 * @param integer number of UTF-8 characters offset (from left)
	 * @param integer (optional) length in UTF-8 characters from offset
	 * 
	 * @return mixed string or FALSE if failure
	 */
	public static function utf8_substr($str, $offset, $length = FALSE){
	    if ( $length === FALSE ) {
	        return mb_substr($str, $offset);
	    } else {
	        return mb_substr($str, $offset, $length);
	    }
	}
	
	/**
	 * Assumes mbstring internal encoding is set to UTF-8
	 * Wrapper around mb_strtolower
	 * Make a string lowercase
	 * Note: The concept of a characters "case" only exists is some alphabets
	 * such as Latin, Greek, Cyrillic, Armenian and archaic Georgian - it does
	 * not exist in the Chinese alphabet, for example. See Unicode Standard
	 * Annex #21: Case Mappings
	 * @param string
	 * @return mixed either string in lowercase or FALSE is UTF-8 invalid
	 */
	public static function utf8_strtolower($str){
	    return mb_strtolower($str);
	}
	
	/**
	 * Assumes mbstring internal encoding is set to UTF-8
	 * Wrapper around mb_strtoupper
	 * Make a string uppercase
	 * Note: The concept of a characters "case" only exists is some alphabets
	 * such as Latin, Greek, Cyrillic, Armenian and archaic Georgian - it does
	 * not exist in the Chinese alphabet, for example. See Unicode Standard
	 * Annex #21: Case Mappings
	 * @param string
	 * 
	 * @return mixed either string in lowercase or FALSE is UTF-8 invalid
	 */
	public static function utf8_strtoupper($str){
	    return mb_strtoupper($str);
	}
	
	/**
	 * Assumes mbstring internal encoding is set to UTF-8
	 * Make a string uppercase
	 * Note: The concept of a characters "case" only exists is some alphabets
	 * such as Latin, Greek, Cyrillic, Armenian and archaic Georgian - it does
	 * not exist in the Chinese alphabet, for example. See Unicode Standard
	 * Annex #21: Case Mappings
	 * @param string
	 * 
	 * @return mixed either string in lowercase or FALSE is UTF-8 invalid
	 */
	public static function utf8_uppertolower($str){
		/*
		$upper = "ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ";
		$lower = "àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ";
		return strtr($str, $upper, $lower);
		$str = "Chủ tịch QH Nguyễn Phú Trọng khai mạc kỳ họp và phát biểu với tư cách Tổng Bí";
		*/
		$upper = array(	"À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă","Ằ","Ắ","Ặ",
						"Ẳ","Ẵ","È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ","Ì","Í",
						"Ị","Ỉ","Ĩ","Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ",
						"Ờ","Ớ","Ợ","Ở","Ỡ","Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử",
						"Ữ","Ỳ","Ý","Ỵ","Ỷ","Ỹ","Đ");
		$lower = array( "à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă","ằ","ắ","ặ",
						"ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề","ế","ệ","ể","ễ","ì","í",
						"ị","ỉ","ĩ","ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ",
						"ờ","ớ","ợ","ở","ỡ","ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử",
						"ữ","ỳ","ý","ỵ","ỷ","ỹ","đ");
		return str_replace($upper,$lower,$str);
	}
}

?>
