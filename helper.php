<?php
/**
 * Contains the helper-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Contains static helper methods for the framework.
 * 
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Helper extends FWS_UtilBase
{
	/**
	 * A convenience method to print the backtrace
	 */
	public static function print_backtrace()
	{
		$bt = FWS_Error_Handler::get_instance()->get_backtrace(debug_backtrace());
		$htmlbt = new FWS_Error_BTPrinter_HTML();
		// print js file, just to be sure
		echo '<script type="text/javascript" src="'.FWS_Path::client_fw().'js/basic.js"></script>'."\n";
		echo $htmlbt->print_backtrace($bt);
	}
	
	/**
	 * Raises a predefined error-message of the given type. This message will be used
	 * to call error().
	 * <br>
	 * You can use one of the following values for $type:
	 * <ul>
	 * 	<li>
	 * 		'instance': a variable does not contain a reference on the expected object.<br>
	 * 		1. parameter name<br>
	 * 		2. the expected type<br>
	 * 		3. the given argument
	 * 	</li>
	 * 	<li>
	 * 		'array','object','scalar','string','float','notnull','notempty':<br>
	 * 		1. parameter name<br>
	 * 		2. the given argument
	 * 	</li>
	 * 	<li>
	 * 		'inarray':
	 * 		1. parameter name<br>
	 * 		2. array of valid values<br>
	 * 		3. the given argument
	 * 	</li>
	 * 	<li>
	 * 		'numeric': a variable has to be numeric<br>
	 * 		1. parameter name<br>
	 * 		2. the given argument
	 * 	</li>
	 * 	<li>
	 * 		'numge0': a variable has to be numeric and >= 0<br>
	 * 		1. parameter name<br>
	 * 		2. the given argument
	 * 	</li>
	 * 	<li>
	 * 		'numgt0': a variable has to be numeric and > 0<br>
	 * 		1. parameter name<br>
	 * 		2. the given argument
	 * 	</li>
	 * 	<li>
	 * 		'numgt1': a variable has to be numeric and > 1<br>
	 * 		1. parameter name<br>
	 * 		2. the given argument
	 * 	</li>
	 * 	<li>
	 * 		'numgex': a variable has to be numeric and >= x<br>
	 * 		1. parameter name<br>
	 * 		2. the minimum value<br>
	 * 		3. the given argument
	 * 	</li>
	 * 	<li>
	 * 		'numbetween': a variable has to be numeric and between x and y<br>
	 * 		1. parameter name<br>
	 * 		2. the value of x (the lowest value allowed)<br>
	 * 		3. the value of y (the highest value allowed)<br>
	 * 		4. the given argument
	 * 	</li>
	 * </ul>
	 * You have to specify additional arguments, depending on the type!
	 *
	 * @param string $type the type
	 * @param mixed $arg2
	 * @param mixed $arg3
	 * @param mixed $arg4
	 * @param mixed $arg5
	 */
	public static function def_error($type,$arg2 = null,$arg3 = null,$arg4 = null,$arg5 = null)
	{
		$msg = '';
		switch($type)
		{
			// instance of a specific class
			case 'instance':
				if(func_num_args() != 4)
					FWS_Helper::error('Invalid number of arguments. 4 required');
				
				list(,$name,$reqtype,$arg) = func_get_args();
				if(is_object($arg))
					$argtype = get_class($arg);
				else
					$argtype = gettype($arg);
				$msg = sprintf(
					'The argument $%s is no instance of "%s" but "%s"',$name,$reqtype,$argtype
				);
				break;
			
			case 'array':
			case 'scalar':
			case 'string':
			case 'float':
			case 'object':
			case 'notempty':
			case 'notnull':
			case 'numeric':
			case 'numge0':
			case 'numgt0':
			case 'numgt1':
			case 'integer':
			case 'intge0':
			case 'intgt0':
			case 'intgt1':
			case 'array>0':
			case 'numarray':
			case 'numarray>0':
			case 'intarray':
			case 'intarray>0':
				if(func_num_args() != 3)
					FWS_Helper::error('Invalid number of arguments. 3 required');
			
				list(,$name,$arg) = func_get_args();
				$arg = self::_get_str_val($arg);
				switch($type)
				{
					case 'object':
					case 'array':
						$stype = 'an '.$type;
						break;
					case 'notnull':
						$stype = 'not null';
						break;
					case 'notempty':
						$stype = 'not empty';
						break;
					case 'array>0':
						$stype = 'a not empty array';
						break;
					case 'numarray':
						$stype = 'a numeric array';
						break;
					case 'numarray>0':
						$stype = 'a not empty, numeric array';
						break;
					case 'numeric':
						$stype = 'numeric';
						break;
					case 'numge0':
						$stype = 'numeric and >= 0';
						break;
					case 'numgt0':
						$stype = 'numeric and > 0';
						break;
					case 'numgt1':
						$stype = 'numeric and > 1';
						break;
					case 'integer':
						$stype = 'an integer';
						break;
					case 'intge0':
						$stype = 'an integer and >= 0';
						break;
					case 'intgt0':
						$stype = 'an integer and > 0';
						break;
					case 'intgt1':
						$stype = 'an integer and > 1';
						break;
					case 'intarray':
						$stype = 'an integer array';
						break;
					case 'intarray>0':
						$stype = 'a not empty, integer array';
						break;
					default:
						$stype = 'a '.$type;
						break;
				}
				$msg = sprintf(
					'The argument $%s (value = "%s") has to be %s',$name,$arg,$stype
				);
				break;
			
			case 'inarray':
				if(func_num_args() != 4)
					FWS_Helper::error('Invalid number of arguments. 4 required');
			
				list(,$name,$valid,$arg) = func_get_args();
				$arg = self::_get_str_val($arg);
				$valid_str = FWS_PrintUtils::to_string($valid,true,false);
				$msg = sprintf(
					'The argument $%s (value = "%s") is invalid. Allowed are: %s',$name,$arg,$valid_str
				);
				break;
			
			// numeric and >= x
			case 'numgex':
				if(func_num_args() != 4)
					FWS_Helper::error('Invalid number of arguments. 4 required');
				
				list(,$name,$min,$arg) = func_get_args();
				$arg = self::_get_str_val($arg);
				$msg = sprintf('The argument $%s (value = "%s") is no number >= %d!',$name,$arg,$min);
				break;
			
			// numeric value between x and y
			case 'numbetween':
				if(func_num_args() != 5)
					FWS_Helper::error('Invalid number of arguments. 5 required');
				
				list(,$name,$min,$max,$arg) = func_get_args();
				$arg = self::_get_str_val($arg);
				$msg = sprintf(
					'The argument $%s (value = "%s") has to be between %d and %d!',$name,$arg,$min,$max
				);
				break;
			
			default:
				FWS_Helper::error('Unknown type: '.$type);
				break;
		}
		
		FWS_Helper::error($msg);
	}
	
	/**
	 * Builds the string-value of the given value for #def_error()
	 *
	 * @param mixed $val the value
	 * @return string the string-value
	 */
	private static function _get_str_val($val)
	{
		if(is_object($val))
			return 'Instance of '.get_class($val);
		if(is_array($val))
		{
			$res = FWS_PrintUtils::to_string($val,true,false);
			$ls = new FWS_HTML_LimitedString($res,20);
			return $ls->get();
		}
		
		return $val === null ? '<i>NULL</i>' : strval($val);
	}
	
	/**
	 * Checks wether the given argument is an integer or for example a string that contains
	 * an integer.
	 *
	 * @param mixed $val the value to check
	 * @return boolean true if so
	 */
	public static function is_integer($val)
	{
		return is_numeric($val) ? intval($val) == $val : false;
	}
	
	/**
	 * Raises an error-message. This is a wrapper for trigger_error() which
	 * supports stopping the script after the error.
	 * 
	 * @param string $message the message to show
	 * @param boolean $die do you want to stop the script?
	 * @param int $level the error-level. See E_USER_*
	 */
	public static function error($message,$die = true,$level = E_USER_ERROR)
	{
		trigger_error($message,$level);
		if($die)
			die();
	}
}
?>