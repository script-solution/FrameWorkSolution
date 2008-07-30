<?php
/**
 * Contains the print-utilities
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This class offers some static methods to print objects, arrays and other types.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_PrintUtils extends FWS_UtilBase
{
	/**
	 * Helper to store the layer. Note that we have to store it here instead of passing
	 * it by parameter because we call the __toString() methods of the objects which
	 * would cause that we loose the layer.
	 *
	 * @var int
	 */
	private static $_layer = 1;
	
	/**
	 * A stack to prevent recursion
	 *
	 * @var array
	 */
	private static $_stack = array();
	
	/**
	 * Builds the string representation for the given object and the given properties.
	 * Note that the properties <b>have to</b> be specified seperatly because we don't have
	 * access at to the private & protected properties.
	 *
	 * @param object $obj the object to print
	 * @param array $properties all properties (see get_object_vars())
	 * @param boolean $use_html use HTML to print it?
	 */
	public static function obj_to_string($obj,$properties,$use_html = true)
	{
		$str = '';
		if($use_html)
			$str .= '<b>'.get_class($obj).'</b>';
		else
			$str .= get_class($obj);
		$str .= '['.self::_to_string($properties,$use_html).']';
		
		if($use_html)
			$str = self::_to_html($str,true);
		
		return $str;
	}

	/**
	 * Builds a string representation of <var>$var</var> recursivly
	 *
	 * @param mixed $var the value
	 * @param boolean $use_html do you want to use HTML?
	 * @param boolean $ml build a multiline string?
	 * @return string the string representation
	 */
	public static function to_string($var,$use_html = true,$ml = true)
	{
		$str = self::_to_string($var,$use_html,$ml);
		if($use_html)
			$str = self::_to_html($str,$ml);
		
		return $str;
	}
	
	/**
	 * Builds a string representation of <var>$var</var> recursivly
	 * 
	 * @param mixed $var the value
	 * @param boolean $use_html do you want to use HTML?
	 * @param boolean $ml build a multiline string?
	 * @return string the string-representation
	 */
	private static function _to_string($var,$use_html,$ml = true)
	{
		$indent = '';
		if($ml)
		{
			for($i = 0;$i < self::$_layer;$i++)
				$indent .= "\t";
		}
		
		$str = '';
		if(is_array($var))
		{
			self::$_layer++;
			
			$str .= '{'.($ml ? "\n" : '');
			foreach($var as $k => $v)
			{
				$str .= $indent.htmlspecialchars($k).' = '.self::_to_string($v,$use_html,$ml);
				if($ml)
					$str .= "\n";
				else
					$str .= ';';
			}
			$str .= FWS_String::substr($indent,0,-1).'}';
			
			self::$_layer--;
		}
		else
		{
			$color = $use_html ? self::_get_type_color($var) : null;
			if(is_string($var) && $use_html)
				$var = htmlspecialchars($var,ENT_QUOTES);
			
			if($var instanceof FWS_Object)
			{
				// detect recursion
				if(in_array($var->get_object_id(),self::$_stack))
					return '<span style="color: red;"><i>*RECURSION*</i></span>';
				
				array_push(self::$_stack,$var->get_object_id());
				$str .= $var->__toString($use_html);
				array_pop(self::$_stack);
			}
			else if(is_object($var))
			{
				$classname = get_class($var);
				$str .= $classname.'[';
				$str .= self::_to_string(get_object_vars($var),$use_html,$ml);
				$str .= ']';
			}
			else if(!is_object($var) && $use_html)
			{
				$str .= '<span style="color: '.$color.';">';
				if(is_bool($var))
					$str .= $var ? 'true' : 'false';
				else if($var === null)
					$str .= 'NULL';
				else
					$str .= $var;
				$str .= '</span>';
			}
			else
				$str .= @strval($var);
		}
		
		return $str;
	}

	/**
	 * Returns the color for the given variable
	 *
	 * @param mixed $var the variable
	 * @return string the color for the variable
	 */
	private static function _get_type_color($var)
	{
		if($var === null)
			return '#000; font-style: italic';
		
		$type = gettype($var);
		switch($type)
		{
			case 'string':
				return '#FF00FF';
			case 'boolean':
				return '#FF0000';
			case 'integer':
				return '#000080';
			case 'double':
				return '#800000';
			case 'resource':
				return '#008000';
			default:
				return '#000000';
		}
	}
	
	/**
	 * Converts the given string to HTML
	 *
	 * @param string $str the input-string
	 * @param boolean $ml build a multiline string?
	 * @return string the result
	 */
	private static function _to_html($str,$ml)
	{
		$str = str_replace("\n",'<br />',$str);
		$str = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$str);
		if(self::$_layer == 1)
		{
			$inline = !$ml ? 'display: inline; ' : '';
			$str = '<div style="'.$inline.'font-family: monospace; font-size: 11px;">'.$str.'</div>';
		}
		return $str;
	}
}
?>