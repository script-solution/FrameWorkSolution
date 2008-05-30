<?php
/**
 * Contains the helper-class
 *
 * @version			$Id: helper.php 740 2008-05-24 09:46:22Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Contains static helper methods for the library.
 * 
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Helper extends PLIB_UtilBase
{
	/**
	 * A convenience method to print the backtrace
	 */
	public static function print_backtrace()
	{
		$bt = PLIB_Error_Handler::get_instance()->get_backtrace(debug_backtrace());
		$htmlbt = new PLIB_Error_BTPrinter_HTML();
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
	 */
	public static function def_error($type)
	{
		$msg = '';
		switch($type)
		{
			// instance of a specific class
			case 'instance':
				if(func_num_args() != 4)
					PLIB_Helper::error('Invalid number of arguments. 4 required');
				
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
					PLIB_Helper::error('Invalid number of arguments. 3 required');
			
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
					PLIB_Helper::error('Invalid number of arguments. 4 required');
			
				list(,$name,$valid,$arg) = func_get_args();
				$arg = self::_get_str_val($arg);
				$valid_str = PLIB_PrintUtils::to_string($valid,true,false);
				$msg = sprintf(
					'The argument $%s (value = "%s") is invalid. Allowed are: %s',$name,$arg,$valid_str
				);
				break;
			
			// numeric and >= x
			case 'numgex':
				if(func_num_args() != 4)
					PLIB_Helper::error('Invalid number of arguments. 4 required');
				
				list(,$name,$min,$arg) = func_get_args();
				$arg = self::_get_str_val($arg);
				$msg = sprintf('The argument $%s (value = "%s") is no number >= %d!',$name,$arg,$min);
				break;
			
			// numeric value between x and y
			case 'numbetween':
				if(func_num_args() != 5)
					PLIB_Helper::error('Invalid number of arguments. 5 required');
				
				list(,$name,$min,$max,$arg) = func_get_args();
				$arg = self::_get_str_val($arg);
				$msg = sprintf(
					'The argument $%s (value = "%s") has to be between %d and %d!',$name,$arg,$min,$max
				);
				break;
			
			default:
				PLIB_Helper::error('Unknown type: '.$type);
				break;
		}
		
		PLIB_Helper::error($msg);
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
			$res = PLIB_PrintUtils::to_string($val,true,false);
			$ls = new PLIB_HTML_LimitedString($res,20);
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
	
	/**
	 * Includes the module given by the action-parameter and returns the name.
	 * This is the default way to handle modules. The method assumes
	 * that the modules are at:
	 * <code>PLIB_Path::inner().$folder.$action.'/module_'.$action.'.php'</code>
	 * The classes have to have the name:
	 * <code>$prefix.$action</code>
	 *
	 * @param string $prefix the prefix for the module-class-names
	 * @param string $action_param the name of the action-get-parameter
	 * @param string $default the default-module-name
	 * @param string $folder the folder of the modules (starting at {@link PLIB_Path::inner()})
	 * @return string the module-name
	 */
	public static function get_module_name($prefix = 'PLIB_Module_',$action_param = 'action',
		$default = 'index',$folder = 'modules/')
	{
		if(empty($action_param))
			PLIB_Helper::def_error('notempty','action_param',$action_param);
		if(empty($default))
			PLIB_Helper::def_error('notempty','default',$default);
		if(!is_dir(PLIB_Path::inner().$folder))
			PLIB_Helper::error('"'.PLIB_Path::inner().$folder.'" is no folder!');
		
		$folder = PLIB_FileUtils::ensure_trailing_slash($folder);
		$action = PLIB_Input::get_instance()->get_var($action_param,'get',PLIB_Input::IDENTIFIER);
	
		// try to load the module
		$filename = PLIB_Path::inner().$folder.$action.'/module_'.$action.'.php';
		if(file_exists($filename))
		{
			include_once($filename);
			if(class_exists($prefix.$action))
				return $action;
		}
	
		// use default module
		include_once(PLIB_Path::inner().$folder.$default.'/module_'.$default.'.php');
		if(class_exists($prefix.$default))
			return $default;
	
		PLIB_Helper::error(
			'Unable to load a module. The default module "'.$default.'" does not exist!',
			E_USER_ERROR
		);
	
		return '';
	}
	
	/**
	 * Determines which standalone-module to use and includes the module.
	 * This is the default way to handle standalone-modules. The method assumes
	 * that the modules are at:
	 * <code>PLIB_Path::inner().$folder.$action.'.php'</code>
	 * The classes have to have the name:
	 * <code>$prefix.$action</code>
	 *
	 * @param PLIB_Document $base the base-object
	 * @param string $prefix the prefix for the module-class-names
	 * @param string $action_param the name of the action-get-parameter
	 * @param string $folder the folder of the standalone-files (starting at
	 * 	{@link PLIB_Path::inner()})
	 * @return string the module-name
	 */
	public static function get_standalone_name($base,$prefix = 'PLIB_Standalone_',
		$action_param = 'action',$folder = 'standalone/')
	{
		if(!($base instanceof PLIB_Document))
			PLIB_Helper::def_error('instance','base','PLIB_Document',$base);
		if(empty($action_param))
			PLIB_Helper::def_error('notempty','action_param',$action_param);
		if(!is_dir(PLIB_Path::inner().$folder))
			PLIB_Helper::error('"'.PLIB_Path::inner().$folder.'" is no folder!');
		
		$action = $base->input->get_var($action_param,'get',PLIB_Input::IDENTIFIER);
	
		// try to load the module
		$filename = PLIB_Path::inner().$folder.$action.'.php';
		if(file_exists($filename))
		{
			include_once($filename);
			if(class_exists($prefix.$action))
				return $action;
		}
	
		PLIB_Helper::error('Unable to load a standalone-module!');
		return '';
	}
	
	/**
	 * Generates the location-string. This is the default way:
	 * <code><home> &raquo; <link1> &raquo; ... &raquo; <linkN></code>
	 * The method uses the method get_location() of the given module.
	 *
	 * You will get an array of the form:
	 * <code>
	 * 	array(
	 * 		'position' => <positionString>,
	 * 		'title' => <positionForDocTitle>
	 * 	)
	 * </code>
	 *
	 * @param PLIB_Module $module the current module
	 * @param string $home_name the name for the home-link
	 * @param string $home_url the URL of the home-link (get_url(-1) by default)
	 * @param string $linkclass the linkclass to use. Use an empty string if you want to use a class.
	 * @return array the position and document-title
	 */
	public static function generate_location($module,$home_name = 'Index',$home_url = null,
		$linkclass = '')
	{
		if(!($module instanceof PLIB_Module))
			PLIB_Helper::def_error('instance','module','PLIB_Module',$module);
		if(empty($home_name))
			PLIB_Helper::def_error('notempty','home_name',$home_name);
	
		$suffix = '';
		$loc = $module->get_location();
		foreach($loc as $name => $url)
		{
			if($url == '')
				$suffix .= ' &raquo; '.$name;
			else
			{
				$suffix .= ' &raquo; <a ';
				if($linkclass)
					$suffix .= 'class="'.$linkclass.'" ';
				$suffix .= 'href="'.$url.'">'.$name.'</a>';
			}
		}
	
		$href = $home_url === null ? $module->url->get_url(-1) : $home_url;
		$loc = '<a ';
		if($linkclass)
			$loc .= 'class="'.$linkclass.'" ';
		$loc .= 'href="'.$href.'">'.$home_name.'</a>';
	
		$loc .= $suffix;
	
		return array(
			'position' => $loc,
			'title' => strip_tags($suffix)
		);
	}
}
?>