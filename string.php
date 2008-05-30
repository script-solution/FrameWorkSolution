<?php
/**
 * Contains a class with string-methods to support multibyte-strings
 *
 * @version			$Id: string.php 744 2008-05-24 15:11:18Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

// set the default value
PLIB_String::set_use_mb_functions(function_exists('mb_strlen'));

/**
 * A class which provides all string-functions that are affected of multibyte issues.
 * If enabled the mb_* functions will be called.
 * If your application uses a multibyte charset you <b>should</b> use this class instead
 * of the default PHP string-functions!
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_String extends PLIB_UtilBase
{
	/**
	 * Indicates wether multibyte-functions should be used
	 * 
	 * @var boolean
	 */
	private static $_use_mb;
	
	/**
	 * @return boolean wether mb-functions are used
	 */
	public static function get_use_mb_functions()
	{
		return PLIB_String::$_use_mb;
	}
	
	/**
	 * Sets wether multibyte functions should be used
	 * 
	 * @param boolean $use_mb the new value
	 * @param string $encoding the encoding to use
	 */
	public static function set_use_mb_functions($use_mb,$encoding = 'UTF-8')
	{
		PLIB_String::$_use_mb = $use_mb;
		if($use_mb)
			mb_internal_encoding($encoding);
	}
	
	/**
	 * Uses the multibyte-version of substr_count() if possible.
	 * 
	 * @param string $haystack the string to search in
	 * @param string $needle the string to search
	 * @return int the number of occurrences of <var>$needle</var> in <var>$haystack</var>
	 */
	public static function substr_count($haystack,$needle)
	{
		if(PLIB_String::$_use_mb)
			return mb_substr_count($haystack,$needle);
		
		return substr_count($haystack,$needle);
	}
	
	/**
	 * Uses the multibyte-version of strtoupper() if possible.
	 * 
	 * @param string $str the input-string
	 * @return string the uppercase-version of the string
	 */
	public static function strtoupper($str)
	{
		if(PLIB_String::$_use_mb)
			return mb_strtoupper($str);
		
		return strtoupper($str);
	}
	
	/**
	 * Uses the multibyte-version of strtolower() if possible.
	 * 
	 * @param string $str the input-string
	 * @return string the lowercase-version of the string
	 */
	public static function strtolower($str)
	{
		if(PLIB_String::$_use_mb)
			return mb_strtolower($str);
		
		return strtolower($str);
	}
	
	/**
	 * Uses the multibyte-version of strrpos() if possible.
	 * 
	 * @param string $haystack the string to search in
	 * @param string $needle the string to search
	 * @param int $offset optional you can specify the start-position
	 * @return the last position of <var>$needle</var> in <var>$haystack</var> or false if
	 * 	not found
	 */
	public static function strrpos($haystack,$needle,$offset = 0)
	{
		if(PLIB_String::$_use_mb)
			return mb_strrpos($haystack,$needle,$offset);
		
		return strrpos($haystack,$needle,$offset);
	}
	
	/**
	 * Uses the multibyte-version of strpos() if possible.
	 * 
	 * @param string $haystack the string to search in
	 * @param string $needle the string to search
	 * @param int $offset optional you can specify the start-position
	 * @return the first position of <var>$needle</var> in <var>$haystack</var> or false if
	 * 	not found
	 */
	public static function strpos($haystack,$needle,$offset = 0)
	{
		if(PLIB_String::$_use_mb)
			return mb_strpos($haystack,$needle,$offset);
		
		return strpos($haystack,$needle,$offset);
	}
	
	/**
	 * Uses the multibyte-version of stripos() if possible.
	 * 
	 * @param string $haystack the string to search in
	 * @param string $needle the string to search
	 * @param int $offset optional you can specify the start-position
	 * @return the first position of <var>$needle</var> in <var>$haystack</var> or false if
	 * 	not found
	 */
	public static function stripos($haystack,$needle,$offset = 0)
	{
		if(PLIB_String::$_use_mb)
			return mb_stripos($haystack,$needle,$offset);
		
		return stripos($haystack,$needle,$offset);
	}
	
	/**
	 * Uses the multibyte-version of strlen() if possible.
	 * 
	 * @param string $str the input-string
	 * @return int the length of the string
	 */
	public static function strlen($str)
	{
		if(PLIB_String::$_use_mb)
			return mb_strlen($str);
		
		return strlen($str);
	}
	
	/**
	 * Uses the multibyte-version of mail() if possible.
	 * It is not recommended to use this method since UTF-8 is not supported by all email-clients!
	 * 
	 * @param string $to the receiver-address
	 * @param string $subject the subject
	 * @param string $message the message to send
	 * @param string $additional_headers the string to be inserted at the end of the email header.
	 * @param string $additional_parameters the additional_parameters parameter can be used to
	 * 	pass an additional parameter to the program configured to use when sending mail using the
	 * 	sendmail_path configuration setting. For example, this can be used to set the envelope
	 * 	sender address when using sendmail with the -f sendmail option.
	 * @return boolean true if successfull
	 */
	public static function mail($to,$subject,$message,$additional_headers = '',
		$additional_parameters = '')
	{
		if(PLIB_String::$_use_mb)
			return mb_send_mail($to,$subject,$message,$additional_headers,$additional_parameters);
		
		return mail($to,$subject,$message,$additional_headers,$additional_parameters);
	}
	
	/**
	 * Uses the multibyte version of substr(), if possible.
	 * 
	 * @param string $str the string
	 * @param int $start the start-position
	 * @param int $len the length (optional)
	 * @return the substring
	 */
	public static function substr($str,$start,$len = null)
	{
		if(PLIB_String::$_use_mb)
		{
			// it seems as if there is no other way than checking here if the parameter
			// has been specified. 0 and null lead to other behavior...
			if($len !== null)
				return mb_substr($str,$start,$len);
			return mb_substr($str,$start);
		}
		
		if($len !== null)
			return substr($str,$start,$len);
		return substr($str,$start);
	}
	
	/**
	 * Determines if <var>$str</var> starts with <var>$start</var>.
	 * You may specify an offset to look at a different location than the string-start.
	 * 
	 * @param string $str the input-string
	 * @param string $start the substring to search for
	 * @param int $offset the optional offset in the string
	 * @return boolean true if the string starts with the given substring
	 */
	public static function starts_with($str,$start,$offset = 0)
	{
		if(!is_string($str))
			PLIB_Helper::def_error('string','str',$str);
		if(!is_string($start))
			PLIB_Helper::def_error('string','start',$start);
		
		$ilen = PLIB_String::strlen($str);
		$slen = PLIB_String::strlen($start);
		if($slen == 0)
			PLIB_Helper::error('strlen($start) has to be > 0!');
		
		if(!PLIB_Helper::is_integer($offset) || $offset < 0 || ($offset >= $ilen && $offset > 0))
			PLIB_Helper::def_error('numbetween','offset',0,$ilen - 1,$offset);
		
		return PLIB_String::substr($str,$offset,$slen) == $start;
	}
	
	/**
	 * Determines if <var>$str</var> ends with <var>$end</var>.
	 *
	 * @param string $str the input-string
	 * @param string $end the substring to search for
	 * @return boolean true if the string ends with the given substring
	 */
	public static function ends_with($str,$end)
	{
		if(!is_string($str))
			PLIB_Helper::def_error('string','str',$str);
		if(!is_string($end))
			PLIB_Helper::def_error('string','end',$end);
		
		$elen = PLIB_String::strlen($end);
		if($elen == 0)
			PLIB_Helper::error('strlen($end) has to be > 0!');
		
		return PLIB_String::substr($str,-$elen) == $end;
	}
	
	/**
	 * Checks wether the given character is whitespace
	 *
	 * @param char $c
	 * @return boolean true if it is whitespace
	 */
	public static function is_whitespace($c)
	{
		return $c == ' ' || $c == "\t" || $c == "\n" || $c == "\r" || $c == "\0" || $c == "\x0B";
	}
	
	/**
	 * Checks wether the given character is an alpha-char
	 *
	 * @param char $c the character
	 * @return boolean true if it is an alpha-char
	 */
	public static function is_alpha($c)
	{
		// a-z
		if($c >= 'a' && $c <= 'z')
			return true;
	
		// A-Z
		if($c >= 'A' && $c <= 'Z')
			return true;
	
		// german umlaute
		if($c == "\xE4" || $c == "\xF6" || $c == "\xFC" ||
			 $c == "\xC4" || $c == "\xD6" || $c == "\xDC" || $c == "\xDF")
		{
			return true;
		}
	
		return false;
	}
}
?>