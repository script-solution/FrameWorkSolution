<?php
/**
 * Contains the string-helper class
 * 
 * @package			FrameWorkSolution
 *
 * Copyright (C) 2003 - 2012 Nils Asmussen
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Contains static helper methods for strings. That means for example methods that
 * generate a string for something or manipulate a string.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_StringHelper extends FWS_UtilBase
{
	/**
	 * Checks wether the given string is a valid "id-string". That means that the array that we get
	 * from <code>FWS_Array_Utils::advanced_explode($sep,$ids)</code> just contains positive integers
	 * and the number of ids is greater than 0.
	 * If it is valid the id-array will be returned. Otherwise false
	 *
	 * @param string $ids the id-string
	 * @param string $sep the id-separator
	 * @return array|boolean the id-array if valid or false
	 */
	public static function get_ids($ids,$sep = ',')
	{
		$ida = FWS_Array_Utils::advanced_explode($sep,$ids);
		if(count($ida) == 0)
			return false;
		
		if(!FWS_Array_Utils::is_integer($ida))
			return false;
		
		return $ida;
	}
	
	/**
	 * Builds an enumeration for the given elements. For example:
	 * <pre>
	 * elements: array(a,b,c)
	 * results in: '"a", "b" and "c"'
	 * </pre>
	 *
	 * @param array $elements the elements
	 * @param string $and the string for "and"
	 * @return string the enumeration
	 */
	public static function get_enum($elements,$and = 'and',$quot = '&quot;')
	{
		$str = '';
		for($i = 0,$len = count($elements);$i < $len;$i++)
		{
			$str .= $quot.$elements[$i].$quot;
			if($i < $len - 2)
				$str .= ', ';
			else if($i < $len - 1)
				$str .= ' '.$and.' ';
		}
		return $str;
	}
	
	/**
	 * Extracts a part of the given text. If there is any text before or behind the text-part
	 * <var>$pre</var> and/or <var>$post</var> will be added.
	 *
	 * @param string $text the text
	 * @param int $pos the position
	 * @param int $count the number of chars to extract
	 * @param string $pre the text to add before
	 * @param string $post the text to add behind
	 * @return string the text-part
	 */
	public static function get_text_part($text,$pos,$count,$pre = '...',$post = '...')
	{
		$len = FWS_String::strlen($text);
		$str = '';
		if($pos > 0)
			$str .= $pre;
		$str .= FWS_String::substr($text,$pos,$count);
		if($pos + $count < $len)
			$str .= $post;
		return $str;
	}
	
	/**
	 * Generates a random key with the given length
	 * the key will contain the chars a-z A-Z 0-9
	 *
	 * @param int $length the length of the key
	 * @return string the key
	 */
	public static function generate_random_key($length = 32)
	{
		if(!FWS_Helper::is_integer($length) || $length <= 0)
			FWS_Helper::def_error('intgt0','length',$length);
	
		$array = array(
			'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u',
			'v','w','x','y','z',
			'1','2','3','4','5','6','7','8','9','0',
			'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U',
			'V','W','X','Y','Z'
		);
		$len = count($array);
		mt_srand((double)microtime() * 1000000);
		$key = '';
		for($i = 0;$i < $length;$i++)
			$key .= $array[mt_rand(0,$len - 1)];
		return $key;
	}
	
	/**
	 * Minimizes the given url to a maximum length
	 *
	 * @param string $url the url
	 * @param int $max the maximum length
	 * @return string the manipulated url
	 */
	public static function minimize_url($url,$max)
	{
		if(!FWS_Helper::is_integer($max) || $max <= 0)
			FWS_Helper::def_error('intgt0','max',$max);
	
		if(empty($url))
			FWS_Helper::def_error('notempty','url',$url);
	
		// check if there is nothing to do
		if(FWS_String::strlen($url) <= $max)
			return $url;
	
		// http:// has not to be displayed, so
		if(FWS_String::substr($url,0,7) == 'http://')
			$url = FWS_String::substr($url,7);
	
		// cut the parameters
		if(FWS_String::strpos($url,'/') !== false)
			$url = strtok($url,'/');
	
		// return if the url is short enough
		if(FWS_String::strlen($url) <= $max)
			return $url;
	
		// otherwise cut a part in the middle
		$end = FWS_String::substr($url,-5,5);
		$front = FWS_String::substr($url,0,$max - 8);
		return $front.'...'.$end;
	}
	
	/**
	 * Corrects the given URL and ensures that the URL starts with http://.
	 * If the URL is not valid (e.g. empty) false will be returned.
	 *
	 * @param string $hp the input-URL
	 * @return string|boolean the corrected URL or false
	 */
	public static function correct_homepage($hp)
	{
		if(FWS_String::substr($hp,0,7) != 'http://')
			$hp = 'http://'.$hp;
		if(preg_match('/^http:\/\/\S+?\.\S+$/',$hp))
			return $hp;
	
		return false;
	}
	
	/**
	 * Checks wether the given email-address is valid
	 *
	 * @param string $mail the email-address to check
	 * @return boolean true if valid
	 */
	public static function is_valid_email($mail)
	{
		// borrowed from php.net :)
		$p = '/^[a-z0-9!#$%&*+-=?^_`{|}~]+(\.[a-z0-9!#$%&*+-=?^_`{|}~]+)*';
		$p.= '@([-a-z0-9]+\.)+([a-z]{2,3}';
		$p.= '|info|arpa|aero|coop|name|museum)$/ix';
		return preg_match($p,$mail);
	}
	
	/**
	 * Formates a data-size.
	 *
	 * @param int $size the size to format
	 * @param string $t_sep the thousands-separator
	 * @param string $d_sep the decimal-places-separator
	 * @return string the formated data-size
	 */
	public static function get_formated_data_size($size,$t_sep = ',',$d_sep = '.')
	{
		if(!FWS_Helper::is_integer($size) || $size < 0)
			FWS_Helper::def_error('intge0','size',$size);
	
		$sizes = array(
			'KiB',
			'MiB',
			'GiB',
			'TiB'
		);
	
		$suff = 'Byte';
		foreach($sizes as $s)
		{
			if($size >= 1024)
			{
				$size /= 1024;
				$suff = $s;
			}
			else
				break;
		}
	
		return number_format($size,3,$d_sep,$t_sep).' '.$suff;
	}
	
	/**
	 * Builds the sql-command for a integer-range
	 *
	 * @param string $field the name of the field
	 * @param int $from the from-value
	 * @param int $to the to-value
	 * @return string the sql-command
	 */
	public static function build_int_range_sql($field,$from,$to)
	{
		if(empty($field))
			FWS_Helper::def_error('notempty','field',$field);
	
		$where = '';
		if($from != '' || $to != '')
		{
			if($from == '')
				$from = -1;
			if($to == '')
				$to = -1;
	
			if($from <= $to || $to == -1)
			{
				if($from >= 0)
					$where .= ' AND '.$field.' >= '.$from;
				if($to >= 0)
					$where .= ' AND '.$field.' <= '.$to;
			}
		}
	
		return $where;
	}
	
	/**
	 * Builds the sql-command for a date-range
	 * 
	 * @param string $field the name of the field
	 * @param string $from the from-value
	 * @param string $to the to-value
	 * @return string the sql-command
	 */
	public static function build_date_range_sql($field,$from,$to)
	{
		if(empty($field))
			FWS_Helper::def_error('notempty','field',$field);
	
		$from = trim($from);
		$to = trim($to);
		$where = '';
		if($from != '' || $to != '')
		{
			if($from != '' && preg_match('/^\d{2}\.\d{2}\.\d{4}$/',$from))
			{
				$parts = explode('.',$from);
				$begin = FWS_Date::get_timestamp(array(0,0,0,$parts[1],$parts[0],$parts[2]));
				$where .= ' AND '.$field.' >= '.$begin;
			}
	
			if($to != '' && preg_match('/^\d{2}\.\d{2}\.\d{4}$/',$to))
			{
				$parts = explode('.',$to);
				$end = FWS_Date::get_timestamp(array(23,59,59,$parts[1],$parts[0],$parts[2]));
				$where .= ' AND '.$field.' <= '.$end;
			}
		}
	
		return $where;
	}

	/**
	 * Checks and returns the clean date-value. The function expects the format DD.MM.YYYY
	 *
	 * @param string $date the date
	 * @return string the clean date
	 */
	public static function get_clean_date($date)
	{
		if(!preg_match('/^\d{2}\.\d{2}\.\d{4}$/',$date))
			return '';
	
		return $date;
	}
		
	/**
	 * Builds the default-sql-query for the delete-info
	 * 
	 * @param array $ids a numeric array with the ids
	 * @param string $table the db-table
	 * @param string $field the field which should be used to show the entries to delete to the user
	 * @return string the sql-query
	 */
	public static function get_default_delete_sql($ids,$table,$field)
	{
		if(!is_array($ids) || count($ids) == 0)
			FWS_Helper::def_error('array>0','ids',$ids);
	
		if(empty($table))
			FWS_Helper::def_error('notempty','table',$table);
	
		if(empty($field))
			FWS_Helper::def_error('notempty','field',$field);
	
		return 'SELECT id,'.$field.' FROM '.$table.' WHERE id IN ("'.implode('","',$ids).'")';
	}

	/**
	 * Determines all words in the given string
	 * will store the words in the keys of the array
	 *
	 * @param string $string the input-string
	 * @return array an associative array of the form:
	 * 	<code>
	 * 		array(<word> => true)
	 * 	</code>
	 */
	public static function get_words($string)
	{
		$break_chars = array(
			' ' => true,"\t" => true,"\r" => true,"\n" => true,',' => true,'.' => true,
			'!' => true,'?' => true,';' => true,'-' => true
		);
		
		$words = array();
		$current = '';
		for($i = 0,$len = FWS_String::strlen($string);$i < $len;$i++)
		{
			$c = FWS_String::substr($string,$i,1);
			
			// skip
			if($c == '&')
			{
				$end = FWS_String::strpos($string,';',$i);
				if($end === false)
					break;
				
				$current .= FWS_String::substr($string,$i,$end - $i + 1);
				$i = $end;
				continue;
			}
	
			if(isset($break_chars[$c]))
			{
				if($current != '' && !is_numeric($current))
				{
					$current = FWS_String::strtolower($current);
					$words[$current] = true;
				}
	
				$current = '';
			}
			else if(FWS_String::is_alpha($c))
				$current .= $c;
		}
	
		if($current != '' && !is_numeric($current))
		{
			$current = FWS_String::strtolower($current);
			$words[$current] = true;
		}
	
		return $words;
	}
	
	/**
	 * replaces the special chars back to the 'real' chars. for example &amp;amp; => &amp;
	 *
	 * @param string $input the input-string
	 * @return string the manipulated string
	 */
	public static function htmlspecialchars_back($input)
	{
		$search = array('&gt;','&lt;','&quot;','&#039;','&amp;');
		$replace = array('>','<','"',"'",'&');
		return str_replace($search,$replace,$input);
	}
	
	/**
	 * limits a string to the given length.
	 * the complete-string will be empty, if the string has not to be cut
	 *
	 * @param string $input the input-string
	 * @param int $length the length you would like to have
	 * @return array an array of the form: <code>array(<limitedString>,<completeString>)</code>
	 * @see FWS_HTML_LimitedString
	 */
	public static function get_limited_string($input,$length)
	{
		if(!FWS_Helper::is_integer($length) || $length <= 0)
			FWS_Helper::def_error('intgt0','length',$length);
	
		$copy = $input;
	
		$input = self::htmlspecialchars_back($input);
		if(FWS_String::strlen($input) > $length)
		{
			$complete = $copy;
			$input = FWS_String::substr($input,0,$length)." ...";
		}
		else
			$complete = '';
	
		return array(htmlspecialchars($input,ENT_QUOTES),$complete);
	}
}
?>