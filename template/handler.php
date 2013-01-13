<?php
/**
 * Contains the template-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	template
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
 * The template-handler to read and display templates.
 * 
 * Currently there are the following constructs available:
 * <ul>
 * 	<li>Variables:				{varName}</li>
 * 	<li>1D-Arrays:				{varName:key}</li>
 * 	<li>2D-Arrays:				{varName:key1:key2}</li>
 * 	<li>3D-Arrays:				{varName:key1:key2:key3}</li>
 * 	<li>Method-calls:			{object.method(<param1>,<param2>,...)}</li>
 * 	<li>Includes:					{include var1~'file'~var2~...)}</li>
 * 	<li>Comments:					{* ... *}</li>
 * 	<li>Conditions:				{if expr} ... [{else} ...]{endif}</li>
 * 	<li>Loops:						{loop array as element} ... {endloop}</li>
 * 	<li>Loops with key:		{loop array as key => element} ... {endloop}</li>
 * 	<li>Loop backwards:		{loopbw array as element} ... {endloop}</li>
 * 	<li>Loop bw with key:	{loopbw array as key => element} ... {endloop}</li>
 * 	<li>Loop a range:			{loop x in a..b} ... {endloop}</li>
 * 	<li>Math operations:	{1 + 1}, {varName * 2}, {2 / varName}, {array.length - 4}, ...</li>
 * 	<li>Array-length:			{array.length}</li>
 * 	<li>Loop-counter:			{array.current}</li>
 * 	<li>Last array index:	{array.last}</li>
 * </ul>
 * Note that by default you have to allow each method to be called from
 * templates via #add_allowed_method(). This gives you control about which
 * functions are called in templates. You can also disable that via #set_limit_method_calls().
 * <br>
 * Additionally you can disable all "advanced" structures such as conditions, loops, includes, ...
 * <br>
 * A usage example:
 * <code>
 * $tpl->set_template('mytemplate.htm');
 * $tpl->add_variable_ref('myarray',$myarray);
 * $tpl->add_variables(array(
 * 	'test1' => 'abc',
 * 	'test2' => 'def'
 * ));
 * echo $tpl->parse_template();
 * </code>
 *
 * @package			FrameWorkSolution
 * @subpackage	template
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Template_Handler extends FWS_Object
{
	/**
	 * Are conditions enabled? (IF,ELSE,ENDIF)
	 *
	 * @var boolean
	 */
	private $_enable_conditions = true;

	/**
	 * Are loops enabled? (LOOP,ENDLOOP)
	 *
	 * @var boolean
	 */
	private $_enable_loops = true;
	
	/**
	 * Are includes enabled?
	 *
	 * @var boolean
	 */
	private $_enable_includes = true;
	
	/**
	 * Are method-calls enabled?
	 *
	 * @var boolean
	 */
	private $_enable_method_calls = true;
	
	/**
	 * Stores wether we want to limit the methods that are callable from templates.
	 *
	 * @var boolean
	 */
	private $_limit_method_calls = true;
	
	/**
	 * Is it allowed to access variables of other templates?
	 *
	 * @var boolean
	 */
	private $_access_to_foreign_tpls = false;
	
	/**
	 * All methods that are callable from templates
	 *
	 * @var array
	 */
	private $_allowed_methods = array();

	/**
	 * The current filename
	 *
	 * @var string
	 */
	private $_filename = '';
	
	/**
	 * The current template-number
	 *
	 * @var int
	 */
	private $_number = 1;

	/**
	 * The cache-folder
	 *
	 * @var string
	 */
	private $_cache_folder = 'cache';

	/**
	 * The available variables
	 *
	 * @var array
	 */
	private $_variables = array();

	/**
	 * The static variables which are available in every template
	 *
	 * @var array
	 */
	private $_static_vars = array();

	/**
	 * The path to the templates-folder
	 *
	 * @var string
	 */
	private $_template_path = '';

	/**
	 * Have we already shown the chmod-warning?
	 *
	 * @var boolean
	 */
	private $_showed_chmod_warning = false;

	/**
	 * The stack will be used to support nesting of template-calls. That means if you want to
	 * parse a template and pass the result to another template which has already been initialized
	 * the stack takes care that this is possible
	 *
	 * @var array
	 */
	private $_stack = array();
	
	/**
	 * A stack that allows us to detect templates that include theirself
	 *
	 * @var array
	 */
	private $_tpl_calls = array();
	
	/**
	 * The number of parse_string()-calls
	 *
	 * @var int
	 */
	private $_string_counter = 1;
	
	/**
	 * Contains the variables for parse_string() while it is called. Otherwise it's null.
	 *
	 * @var array
	 */
	private $_string_vars = null;

	/**
	 * constructor
	 *
	 * @param string $path the path to the templates-folder (with the trailing slash)
	 */
	public function __construct($path = '')
	{
		parent::__construct();
		
		if($path != '')
			$this->set_path($path);
		$this->_cache_folder = FWS_Path::server_app().'cache';
	}
	
	/**
	 * Returns the currently set template which will get all added variables and so on
	 *
	 * @return string the template-name
	 */
	public function get_current_template()
	{
		return $this->_filename;
	}
	
	/**
	 * Returns the currently set template-number which will get all added variables and so on.
	 *
	 * @return int the number
	 */
	public function get_current_number()
	{
		return $this->_number;
	}

	/**
	 * @return string the path to the templates-folder (with the trailing slash)
	 */
	public function get_path()
	{
		return $this->_template_path;
	}

	/**
	 * Sets the path to the template-folder. If the path is empty the method
	 * {@link FWS_User_Current::get_theme_item_path()} will be used and it will be assumed
	 * that in the theme-folder is a folder called "templates" which contains
	 * all templates.
	 *
	 * @param string $path the path to the templates-folder (with the trailing slash and starting
	 * 	at FWS_Path::server_app())
	 */
	public function set_path($path)
	{
		if(!is_dir(FWS_Path::server_app().$path))
			FWS_Helper::error('"'.$path.'" is no folder!');
		
		$this->_template_path = $path;
	}

	/**
	 * @return string the cache-folder
	 */
	public function get_cache_folder()
	{
		return $this->_cache_folder;
	}

	/**
	 * Sets the cache-folder. Without trailing slash!
	 *
	 * @param string $folder the new value
	 */
	public function set_cache_folder($folder)
	{
		if(!is_dir($folder))
			FWS_Helper::error('"'.$folder.'" is no folder!');
		
		$this->_cache_folder = FWS_FileUtils::ensure_no_trailing_slash($folder);
	}

	/**
	 * @return boolean wether conditions are enabled
	 */
	public function get_conditions_enabled()
	{
		return $this->_enable_conditions;
	}

	/**
	 * Sets wether conditions are enabled
	 *
	 * @param boolean $enabled wether conditions are enabled
	 */
	public function set_conditions_enabled($enabled)
	{
		$this->_enable_conditions = (bool)$enabled;
	}

	/**
	 * @return boolean wether loops are enabled
	 */
	public function get_loops_enabled()
	{
		return $this->_enable_loops;
	}

	/**
	 * Sets wether loops are enabled
	 *
	 * @param boolean $enabled wether loops are enabled
	 */
	public function set_loops_enabled($enabled)
	{
		$this->_enable_loops = (bool)$enabled;
	}

	/**
	 * @return boolean wether includes are enabled
	 */
	public function get_includes_enabled()
	{
		return $this->_enable_includes;
	}

	/**
	 * Sets wether includes are enabled
	 *
	 * @param boolean $enabled wether includes are enabled
	 */
	public function set_includes_enabled($enabled)
	{
		$this->_enable_includes = (bool)$enabled;
	}

	/**
	 * @return boolean wether method-calls are enabled
	 */
	public function get_method_calls_enabled()
	{
		return $this->_enable_method_calls;
	}

	/**
	 * Sets wether method-calls are enabled
	 *
	 * @param boolean $enabled wether method-calls are enabled
	 */
	public function set_method_calls_enabled($enabled)
	{
		$this->_enable_method_calls = (bool)$enabled;
	}

	/**
	 * @return boolean wether method-calls are limited
	 */
	public function get_limit_method_calls()
	{
		return $this->_limit_method_calls;
	}

	/**
	 * Sets wether method-calls should be limited.
	 * This prevents that all methods of the specified objects may be used. So you can control
	 * which methods may be called and which not.
	 *
	 * @param boolean $limit wether method-calls should be limited.
	 */
	public function set_limit_method_calls($limit)
	{
		$this->_limit_method_calls = (bool)$limit;
	}
	
	/**
	 * @return boolean wether it is possible to access variables of foreign templates
	 */
	public function get_access_to_foreign_tpls()
	{
		return $this->_access_to_foreign_tpls;
	}
	
	/**
	 * Sets wether it is possible to access variables of foreign templates
	 *
	 * @param boolean $access the new value
	 */
	public function set_access_to_foreign_tpls($access)
	{
		$this->_access_to_foreign_tpls = (bool)$access;
	}
	
	/**
	 * Returns an array with all methods that are callable from templates:
	 * <code>
	 * 	array(<object1>.<method1>,<object1>.<method2>,...,<object2>.<method1>,...)
	 * </code>
	 * 
	 * @return array the callable methods
	 */
	public function get_allowed_methods()
	{
		return $this->_allowed_methods;
	}
	
	/**
	 * Checks wether the given method of the given object may be called. If not
	 * {@link FWS_Helper::error()} will be called (-> end of the script).
	 * This method is intended for the generated templates and should not be called
	 * from anybody else.
	 *
	 * @param string $object the object-name
	 * @param string $method the method-name
	 * @return boolean true if everything is ok
	 */
	public function check_allowed_method($object,$method)
	{
		// don't call the methods here to speed up this method
		if(!$this->_limit_method_calls)
			return true;
		
		if(isset($this->_allowed_methods[$object.'.*']))
			return true;
		
		if(!isset($this->_allowed_methods[$object.'.'.$method]))
			FWS_Helper::error('It is not allowed to call the method "'.$object.'.'.$method.'()"'
				.' from templates! (In template "'.$this->_filename.'")');
		
		return true;
	}
	
	/**
	 * Checks wether the given method of the given object may be called
	 *
	 * @param string $object the object-name
	 * @param string $method the method-name
	 * @return boolean true if so
	 */
	public function is_allowed_method($object,$method)
	{
		if(!is_string($object))
			FWS_Helper::def_error('scalar','object',$object);
		if(!is_string($method))
			FWS_Helper::def_error('string','method',$method);
		
		if(isset($this->_allowed_methods[$object.'.*']))
			return true;
		
		return isset($this->_allowed_methods[$object.'.'.$method]);
	}
	
	/**
	 * Adds the given method of the given object (the name in the template) to the allowed
	 * methods. You may use '*' as wildcard for all methods for <var>$method</var>.
	 * 
	 * @param string $object the name of the object in the template
	 * @param string $method the method-name
	 */
	public function add_allowed_method($object,$method)
	{
		if(!is_string($object))
			FWS_Helper::def_error('scalar','object',$object);
		if(!is_string($method))
			FWS_Helper::def_error('string','method',$method);
		
		$this->_allowed_methods[$object.'.'.$method] = true;
	}
	
	/**
	 * Removes all allowed methods for the given object
	 *
	 * @param string $object the name of the object in the template
	 */
	public function remove_allowed_methods($object)
	{
		foreach(array_keys($this->_allowed_methods) as $k)
		{
			list($o,) = explode('.',$k);
			if($o == $object)
				unset($this->_allowed_methods[$k]);
		}
	}
	
	/**
	 * Removes the given method of the given object (the name in the template) from the allowed
	 * methods. You may use '*' as wildcard for all methods for <var>$method</var>.
	 * 
	 * @param string $object the name of the object in the template
	 * @param string $method the method-name
	 */
	public function remove_allowed_method($object,$method)
	{
		if(!is_string($object))
			FWS_Helper::def_error('scalar','object',$object);
		if(!is_string($method))
			FWS_Helper::def_error('string','method',$method);
		
		unset($this->_allowed_methods[$object.'.'.$method]);
	}

	/**
	 * Sets the template to use for the following operations
	 *
	 * @param string $filename the name of the template (without path)
	 * @param int $number the number of the template (if one template is used more than once)
	 */
	public function set_template($filename,$number = 1)
	{
		if(empty($filename))
			FWS_Helper::def_error('notempty','filename',$filename);
		if(!FWS_Helper::is_integer($number) || $number <= 0)
			FWS_Helper::def_error('intgt0','number',$number);

		// save the current values to the stack
		if($this->_filename != '')
			array_push($this->_stack,array($this->_filename,$this->_number));

		$this->_filename = $filename;
		$this->_number = $number;
		// init the variables, if not already done
		if(!isset($this->_variables[$filename.$number]))
			$this->_variables[$filename.$number] = array();
	}
	
	/**
	 * Restores the latest template. The "undo"-operation for set_template().
	 */
	public function restore_template()
	{
		if(count($this->_stack))
			list($this->_filename,$this->_number) = array_pop($this->_stack);
	}
	
	/**
	 * Adds the given variable to all templates ("global").
	 * Note the name has to be an identifier, that means it has match the
	 * regex '^[a-zA-Z0-9_]+$'. This will <b>not</b> be checked for performance issues!
	 * 
	 * @param string $name the name of the variable
	 * @param mixed $value the value to add
	 */
	public function add_global($name,$value)
	{
		if(empty($name))
			FWS_Helper::def_error('empty','name',$name);

		$this->_static_vars[$name] = $value;
	}
	
	/**
	 * Adds the reference to the given variable to all templates ("global").
	 * Note the name has to be an identifier, that means it has match the
	 * regex '^[a-zA-Z0-9_]+$'. This will <b>not</b> be checked for performance issues!
	 * 
	 * @param string $name the name of the variable
	 * @param mixed $value the value to add
	 */
	public function add_global_ref($name,&$value)
	{
		if(empty($name))
			FWS_Helper::def_error('empty','name',$name);
		if($value === null)
			FWS_Helper::def_error('notnull','value',$value);

		$this->_static_vars[$name] = &$value;
	}

	/**
	 * Adds the given variable by reference to the template-vars. Note the name has to be an identifier,
	 * that means it has match the regex '^[a-zA-Z0-9_]+$'. This will <b>not</b> be checked
	 * for performance issues!
	 *
	 * @param string $name the name of the variable
	 * @param mixed $value the value
	 * @param string $template the template to add the variables to (if not set the
	 * 		current one will be used)
	 * @param int $number the number of the template (if one template is used more than once)
	 * 	(0 = the current one)
	 */
	public function add_variable_ref($name,&$value,$template = '',$number = 0)
	{
		if(empty($name))
			FWS_Helper::def_error('empty','name',$name);
		if($value === null)
			FWS_Helper::def_error('notnull','value',$value);
		
		$tpl = $template != '' ? $template : $this->_filename;
		if(empty($tpl))
			FWS_Helper::def_error('notempty','template',$template);
		
		$number = $number == 0 ? $this->_number : $number;
		if(!FWS_Helper::is_integer($number) || $number <= 0)
			FWS_Helper::def_error('intgt0','number',$number);
		
		$this->_variables[$tpl.$number][$name] = &$value;
	}

	/**
	 * Adds the given variables to the available template-vars. Note that the keys
	 * of the array (the names of the variables) have to be identifiers, that means
	 * they have match the regex '^[a-zA-Z0-9_]+$'. This will <b>not</b> be checked
	 * for performance issues!
	 *
	 * @param array $vars an associative array with the values
	 * @param string $template the template to add the variables to (if not set the
	 * 		current one will be used)
	 * @param int $number the number of the template (if one template is used more than once)
	 * 	(0 = the current one)
	 */
	public function add_variables($vars,$template = '',$number = 0)
	{
		if(is_array($vars))
		{
			$tpl = $template != '' ? $template : $this->_filename;
			if(empty($tpl))
				FWS_Helper::def_error('notempty','template',$template);
			
			$number = $number == 0 ? $this->_number : $number;
			if(!FWS_Helper::is_integer($number) || $number <= 0)
				FWS_Helper::def_error('intgt0','number',$number);
			
			foreach($vars as $name => $value)
			{
				$value = $value === null ? '' : $value;
				$this->_variables[$tpl.$number][$name] = $value;
			}
		}
	}

	/**
	 * Returns the variables including all global ones from the given template
	 * 
	 * @param string $template the template-filename
	 * @param int $number the number of the template (if one template is used more than once)
	 * @return array all variables for the given template
	 */
	public function get_variables($template,$number = 1)
	{
		// return the variables for parse_string()?
		if($this->_string_vars !== null)
			return array_merge($this->_static_vars,$this->_string_vars);
		
		if(empty($template))
			FWS_Helper::def_error('notempty','template',$template);
		if(!FWS_Helper::is_integer($number) || $number <= 0)
			FWS_Helper::def_error('intgt0','number',$number);
		
		$vars = $this->_static_vars;
		if(isset($this->_variables[$template.$number]))
			$vars = array_merge($vars,$this->_variables[$template.$number]);
		return $vars;
	}
	
	/**
	 * Parses the given string independendly of everything else. You can use everything except
	 * includes and you can use all given variables and the global ones.
	 * <p>
	 * That means that this method converts the given string to PHP-code, sets the given
	 * variables and the global ones and evals it. You will get the result text.
	 *
	 * @param string $string the string to parse
	 * @param array $vars an array of variables that should be set for the template
	 * @return string the result-text
	 */
	public function parse_string($string,$vars)
	{
		$this->_string_vars = $vars;
		$incson = $this->get_includes_enabled();
		$this->set_includes_enabled(false);
		
		$parser = new FWS_Template_Parser($this);
		$result = $parser->compile_template('__string_'.$this->_string_counter,'',null,$string);
		eval(FWS_String::substr($result,5,-2));
		
		$func_name = $this->get_function_name('__string_'.$this->_string_counter);
		$str = $func_name($this,1);
		
		$this->set_includes_enabled($incson);
		$this->_string_vars = null;
		$this->_string_counter++;
		
		return $str;
	}

	/**
	 * Parses the given template or the current one
	 *
	 * @param string $template the template-filename
	 * @param boolean $restore restore the last template?
	 * @param int $number the number of the template (if one template is used more than once)
	 * @return string the html-code
	 */
	public function parse_template($template = -1,$restore = true,$number = 1)
	{
		$user = FWS_Props::get()->user();
		
		if(!FWS_Helper::is_integer($number) || $number <= 0)
			FWS_Helper::def_error('intgt0','number',$number);
		
		$recompile_necessary = false;
		// Note that we use -1 as default value to prevent recursion in template-calls
		// Because if a variable has been used for the template-name and it is empty
		// or unset it will be the default value if we use '' and the original template
		// gets parsed again which ends in recursion.
		$tpl = $template !== -1 ? $template : $this->_filename;
		
		// the template must not be empty!
		if(empty($tpl))
			FWS_Helper::def_error('notempty','tpl',$tpl);
		
		// store template-name for error-message
		$old_tpl = $this->_filename;
		$this->_filename = $tpl;
		
		// set template-path if not already done
		$template_path = $this->_template_path;
		if($template_path == '')
		{
			$tp = str_replace(
				FWS_Path::client_app(),'',$user->get_theme_item_path('templates/'.$tpl)
			);
			$template_path = dirname($tp).'/';
		}
		
		// do we have already cached and included the template?
		$func_name = $this->get_function_name($template_path.$tpl);
		if(!function_exists($func_name))
		{
			$cache_path = $this->_get_cache_path($template_path,$tpl);
			
			// check if we have to recompile the template
			if(!file_exists($cache_path))
				$recompile_necessary = true;
			else
			{
				$cache_mtime = filemtime($cache_path);
				$tpl_mtime = filemtime(FWS_Path::server_app().$template_path.$tpl);
				
				// compare the last-modified-times
				if($tpl_mtime > $cache_mtime)
					$recompile_necessary = true;
			}
		}
		
		// recompile?
		if($recompile_necessary)
			$this->_recompile($tpl,$template_path,$cache_path);
		
		// detect recursions
		$tplc = count($this->_tpl_calls);
		if($tplc > 0 && $this->_tpl_calls[$tplc - 1] == $tpl)
			die('It seems that the template "'.$tpl.'" includes itself :)');
		
		array_push($this->_tpl_calls,$tpl);

		// include the cached file (just once)
		if(isset($cache_path))
			include_once($cache_path);
		
		// call the function with corresponding part-argument
		$func_name = $this->get_function_name($template_path.$tpl);
		
		// if the function does not exist there is something wrong, so recompile
		if(!function_exists($func_name))
		{
			$cache_path = $this->_get_cache_path($template_path,$tpl);
			$this->_recompile($tpl,$template_path,$cache_path);
			include($cache_path);
		}
		
		$str = $func_name($this,$number);
		
		array_pop($this->_tpl_calls);
		
		// restore old name
		$this->_filename = $old_tpl;
		
		if($restore)
			$this->restore_template();

		return $str;
	}
	
	/**
	 * Determines the template-path and cache-path for the given template
	 *
	 * @param string $template_path the path to the template
	 * @param string $tpl the template-name
	 * @return string the cache-path
	 */
	private function _get_cache_path($template_path,$tpl)
	{
		// check if the file exists
		if(!is_file(FWS_Path::server_app().$template_path.$tpl))
			FWS_Helper::error('"'.FWS_Path::server_app().$template_path.$tpl.'" is no file!');
		
		$path = str_replace('/','_',$template_path);
		$path = str_replace('\\','_',$path);
		return $this->_cache_folder.'/'.$path.$tpl.'.php';
	}
	
	/**
	 * Recompiles the given template
	 *
	 * @param string $tpl the template
	 * @param string $tplpath the template-path
	 * @param string $cache_path the path of the cache-file
	 */
	private function _recompile($tpl,$tplpath,$cache_path)
	{
		$tpl_content = FWS_FileUtils::read(FWS_Path::server_app().$tplpath.$tpl);
		// if we could not save the template we eval the code directly
		// this prevents problems if the cache-directory has not yet CHMOD 0777 (which may happen
		// at the beginning of the installation)
		$parser = new FWS_Template_Parser($this);
		$tpl_content = $parser->compile_template($tplpath,$tpl,$cache_path,$tpl_content);
		if($tpl_content !== '')
		{
			// show the warning just once
			if(!$this->_showed_chmod_warning)
			{
				echo '<b><span style="color: red;">WARNUNG: Das Verzeichnis "'.dirname($cache_path)
						.'" oder Dateien in diesem Verzeichnis sind nicht beschreibbar.'
						.' Bitte setzen Sie den CHMOD des Verzeichnisses auf 0777 bzw. l&ouml;schen Sie die'
						.' Dateien in diesem Verzeichnis!'
						.'</span></b><br />'."\n";
				echo '<b><span style="color: red;">WARNING: The directory "'.dirname($cache_path)
						.'" or files in this directory are not writable!'
						.' Please set the CHMOD of this directory to 0777 or delete the files in it!'
						.'</span></b><br />'."\n";
				$this->_showed_chmod_warning = true;
			}

			// if the class is already defined, don't define it again
			// (don't use the autoloader here because the generated classes don't follow the naming-rules)
			if(class_exists($this->get_function_name($tplpath.$tpl),false))
			{
				// we don't want to eval the php start- and end-tags
				eval(FWS_String::substr($tpl_content,5,FWS_String::strlen($tpl_content) - 2));
			}
		}
	}
	
	/**
	 * @param string $template the template to use
	 * @return string the function-name for the given template which will be used
	 */
	public function get_function_name($template)
	{
		return 'FWS_TPL_'.preg_replace('/[^a-z0-9_]/i','_',$template);
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>