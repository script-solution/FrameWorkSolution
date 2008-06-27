<?php
/**
 * Contains helper for arrays
 *
 * @version			$Id:utils.php 153 2007-12-10 22:53:09Z nasmussen $
 * @package			PHPLib
 * @subpackage	array
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Contains helper methods for arrays
 * 
 * @package			PHPLib
 * @subpackage	array
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Array_Utils extends PLIB_UtilBase
{
	/**
	 * Converts the given 1-dimension array to a 2-dimension array with a given number of items
	 * per line. So for example:
	 * <code>
	 * 	PLIB_Array_Utils::convert_to_2d(array(1,2,3,4,5,6),4);
	 * </code>
	 * will lead to:
	 * <code>
	 * 	array(
	 * 		array(1,2,3,4),
	 * 		array(5,6)
	 *	)
	 * </code>
	 *
	 * @param array $array the 1-dimensional input-array
	 * @param int $perline the number of items per line
	 * @return unknown
	 */
	public static function convert_to_2d($array,$perline)
	{
		if(!is_array($array))
			PLIB_Helper::def_error('array','array',$array);
		if(!PLIB_Helper::is_integer($perline) || $perline <= 0)
			PLIB_Helper::def_error('intgt0','perline',$perline);
		
		$i = 0;
		$a = array();
		$row = null;
		foreach($array as $element)
		{
			if($i % $perline == 0)
			{
				$a[] = array();
				$row = &$a[count($a) - 1];
			}
			
			$row[] = $element;
			$i++;
		}
		
		return $a;
	}
	
	/**
	 * Filters the given 2-dimensional array and just keeps the keys that appear in
	 * <var>$elements</var>. For example:
	 * <pre>filter_2dim(array(array('a' => 1,'b' => 2)),array('a'))</pre>
	 * will lead to:
	 * <pre>array(array('a' => 1))</pre>
	 *
	 * @param array $array the 2-dimensional array
	 * @param array $elements the element-names to keep
	 * @return array the filtered array
	 */
	public static function filter_2dim($array,$elements)
	{
		if(!is_array($array))
			PLIB_Helper::def_error('array','array',$array);
		if(!is_array($elements))
			PLIB_Helper::def_error('array','elements',$elements);
		
		$qelements = self::get_fast_access($elements);
		$pra = array();
		foreach($array as $k => $v)
		{
			if(!is_array($v))
				PLIB_Helper::error('The element with key "'.$k.'" is no array!');
			
			$sub = array();
			foreach($v as $kk => $vv)
			{
				if(isset($qelements[$kk]))
					$sub[$kk] = $vv;
			}
			$pra[$k] = $sub;
		}
		
		return $pra;
	}
	
	/**
	 * Transforms the given array to a "fast-access-array". That means that all values
	 * of the given array will be taken as key and the value will be just <var>true</var>.
	 * This gives you the opportunity to use <var>isset($array[$key])</var> instead of
	 * <var>in_array($key,$array)</var> to determine wether an element exists.
	 * <br>
	 * Note that this means that duplicate elements will be merged into one and that the
	 * elements have to be scalar!
	 *
	 * @param array $array the array to transform
	 * @return array the "fast-access-array"
	 */
	public static function get_fast_access($array)
	{
		$fast = array();
		$i = 0;
		foreach($array as $v)
		{
			if(!is_scalar($v))
				PLIB_Helper::error('The element '.$i.' is not scalar!');
			
			$fast[$v] = true;
			$i++;
		}
		return $fast;
	}
	
	/**
	 * works like explode() but removes empty parts at the beginning and end
	 *
	 * @param string $split the string to use for splitting the string
	 * @param string $string the input-string
	 * @return array an array with the parts
	 */
	public static function advanced_explode($split,$string)
	{
		if(empty($split))
			PLIB_Helper::def_error('notempty','split',$split);
	
		if($string == '' || $string == null)
			return array();
	
		$split_len = PLIB_String::strlen($split);
		if(PLIB_String::substr($string,0,$split_len) == $split)
			$string = PLIB_String::substr($string,$split_len);
	
		if(PLIB_String::substr($string,-$split_len,$split_len) == $split)
			$string = PLIB_String::substr($string,0,-$split_len);
	
		return explode($split,$string);
	}
	
	/**
	 * works like implode() but excludes empty array-entries
	 *
	 * @param string $separator the separator for the result-string
	 * @param array $array the input-array
	 * @return string a string with the elements of the given array separated by the given separator
	 */
	public static function advanced_implode($separator,$array)
	{
		if(!is_array($array))
			PLIB_Helper::def_error('array','array',$array);
		
		$result = '';
		$len = count($array);
		for($i = 0;$i < $len;$i++)
		{
			if($array[$i] != '')
			{
				if($result != '')
					$result .= $separator;
				$result .= $array[$i];
			}
		}
	
		return $result;
	}
	
	/**
	 * Converts all elements in the array to lower-case. Assumes that the
	 * array has just one dimension and contains strings only.
	 * 
	 * @param array $array the array to be modified
	 * @return array the result-array
	 */
	public static function to_lower(&$array)
	{
		if(is_array($array))
		{
			foreach($array as $k => $element)
				$array[$k] = PLIB_String::strtolower($element);
		}
		
		return $array;
	}
	
	/**
	 * Converts all elements in the array to upper-case. Assumes that the
	 * array has just one dimension and contains strings only.
	 * 
	 * @param array $array the array to be modified
	 * @return array the result-array
	 */
	public static function to_upper(&$array)
	{
		if(is_array($array))
		{
			foreach($array as $k => $element)
				$array[$k] = PLIB_String::strtoupper($element);
		}
		
		return $array;
	}
	
	/**
	 * Trims all strings in the given array. Assumes that the array has just one
	 * dimension and contains strings only.
	 *
	 * @param array [reference] $array the input-array containing the strings
	 * @return array the result-array
	 */
	public static function trim(&$array)
	{
		if(is_array($array))
		{
			foreach($array as $k => $element)
				$array[$k] = trim($element);
		}
		
		return $array;
	}
	
	/**
	 * Checks wether the given array contains numeric values only
	 *
	 * @param array $array the array you want to check
	 * @return boolean true if the array contains numberic values only
	 */
	public static function is_numeric($array)
	{
		if(is_array($array))
		{
			foreach($array as $value)
			{
				if(!is_numeric($value))
					return false;
			}
	
			return true;
		}
	
		return false;
	}
	
	/**
	 * Checks wether the given array contains integers only
	 *
	 * @param array $array the array you want to check
	 * @return boolean true if the array contains integers only
	 */
	public static function is_integer($array)
	{
		if(is_array($array))
		{
			foreach($array as $value)
			{
				if(!PLIB_Helper::is_integer($value))
					return false;
			}
	
			return true;
		}
	
		return false;
	}
}
?>