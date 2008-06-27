<?php
/**
 * Contains the template-class
 *
 * @version			$Id:handler.php 86 2007-11-30 21:41:47Z nasmussen $
 * @package			PHPLib
 * @subpackage	template
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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
 * $tpl->add_array('myarray',$myarray);
 * $tpl->add_variables(array(
 * 	'test1' => 'abc',
 * 	'test2' => 'def'
 * ));
 * echo $tpl->parse_template();
 * </code>
 *
 * @package			PHPLib
 * @subpackage	template
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Template_Handler extends PLIB_FullObject
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
	 * constructor
	 *
	 * @param string $path the path to the templates-folder (with the trailing slash)
	 */
	public function __construct($path = '')
	{
		parent::__construct();
		
		if($path != '')
			$this->set_path($path);
		$this->_cache_folder = PLIB_Path::inner().'cache';
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
	 * @return string the path to the templates-folder (with the trailing slash)
	 */
	public function get_path()
	{
		return $this->_template_path;
	}

	/**
	 * Sets the path to the template-folder. If the path is empty the method
	 * {@link PLIB_User_Current::get_theme_item_path()} will be used and it will be assumed
	 * that in the theme-folder is a folder called "templates" which contains
	 * all templates.
	 *
	 * @param string $path the path to the templates-folder (with the trailing slash)
	 */
	public function set_path($path)
	{
		if(!is_dir($path))
			PLIB_Helper::error('"'.$path.'" is no folder!');
		
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
			PLIB_Helper::error('"'.$folder.'" is no folder!');
		
		$this->_cache_folder = PLIB_FileUtils::ensure_no_trailing_slash($folder);
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
	 * @param boolean $enabled wether method-calls should be limited.
	 */
	public function set_limit_method_calls($limit)
	{
		$this->_limit_method_calls = (bool)$limit;
	}
	
	/**
	 * Returns an array with all methods that are callable from templates:
	 * <code>
	 * 	array(<object1>.<method1>,<object1>.<method2>,...,<object2>.<method1>,...)
	 * </code>
	 * 
	 * @return the callable methods
	 */
	public function get_allowed_methods()
	{
		return $this->_allowed_methods;
	}
	
	/**
	 * Checks wether the given method of the given object may be called. If not
	 * {@link PLIB_Helper::error()} will be called (-> end of the script).
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
			PLIB_Helper::error('It is not allowed to call the method "'.$object.'.'.$method.'()"'
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
			PLIB_Helper::def_error('scalar','object',$object);
		if(!is_string($method))
			PLIB_Helper::def_error('string','method',$method);
		
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
			PLIB_Helper::def_error('scalar','object',$object);
		if(!is_string($method))
			PLIB_Helper::def_error('string','method',$method);
		
		$this->_allowed_methods[$object.'.'.$method] = true;
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
			PLIB_Helper::def_error('scalar','object',$object);
		if(!is_string($method))
			PLIB_Helper::def_error('string','method',$method);
		
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
			PLIB_Helper::def_error('notempty','filename',$filename);
		if(!PLIB_Helper::is_integer($number) || $number <= 0)
			PLIB_Helper::def_error('intgt0','number',$number);

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
	 * @param array $value the value to add
	 */
	public function add_global($name,$value)
	{
		if(empty($name))
			PLIB_Helper::def_error('empty','name',$name);

		$this->_static_vars[$name] = $value;
	}
	
	/**
	 * Adds the reference to the given variable to all templates ("global").
	 * Note the name has to be an identifier, that means it has match the
	 * regex '^[a-zA-Z0-9_]+$'. This will <b>not</b> be checked for performance issues!
	 * 
	 * @param string $name the name of the variable
	 * @param array $value the value to add
	 */
	public function add_global_ref($name,&$value)
	{
		if(empty($name))
			PLIB_Helper::def_error('empty','name',$name);
		if($value === null)
			PLIB_Helper::def_error('notnull','value',$value);

		$this->_static_vars[$name] = &$value;
	}

	/**
	 * Adds the given array to the available template-vars. Note the name has to be an identifier,
	 * that means it has match the regex '^[a-zA-Z0-9_]+$'. This will <b>not</b> be checked
	 * for performance issues!
	 *
	 * @param string $name the name of the variable
	 * @param mixed $array reference to the array
	 * @param string $template the template to add the variables to (if not set the
	 * 		current one will be used)
	 * @param int $number the number of the template (if one template is used more than once)
	 * 	(0 = the current one)
	 */
	public function add_array($name,&$array,$template = '',$number = 0)
	{
		if(empty($name))
			PLIB_Helper::def_error('empty','name',$name);
		if($array === null)
			PLIB_Helper::def_error('notnull','array',$array);
		
		$tpl = $template != '' ? $template : $this->_filename;
		if(empty($tpl))
			PLIB_Helper::def_error('notempty','template',$template);
		
		$number = $number == 0 ? $this->_number : $number;
		if(!PLIB_Helper::is_integer($number) || $number <= 0)
			PLIB_Helper::def_error('intgt0','number',$number);
		
		$this->_variables[$tpl.$number][$name] = &$array;
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
				PLIB_Helper::def_error('notempty','template',$template);
			
			$number = $number == 0 ? $this->_number : $number;
			if(!PLIB_Helper::is_integer($number) || $number <= 0)
				PLIB_Helper::def_error('intgt0','number',$number);
			
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
	public function &get_variables($template,$number = 1)
	{
		if(empty($template))
			PLIB_Helper::def_error('notempty','template',$template);
		if(!PLIB_Helper::is_integer($number) || $number <= 0)
			PLIB_Helper::def_error('intgt0','number',$number);
		
		$vars = $this->_static_vars;
		if(isset($this->_variables[$template.$number]))
			$vars = array_merge($vars,$this->_variables[$template.$number]);
		return $vars;
	}

	/**
	 * Parses the given template or the current one
	 *
	 * @param string $template the template-filename
	 * @param boolean $restore restore the last template?
	 * @param int $number the number of the template (if one template is used more than once)
	 * @return the html-code
	 */
	public function parse_template($template = -1,$restore = true,$number = 1)
	{
		if(!PLIB_Helper::is_integer($number) || $number <= 0)
			PLIB_Helper::def_error('intgt0','number',$number);
		
		$recompile_necessary = false;
		// Note that we use -1 as default value to prevent recursion in template-calls
		// Because if a variable has been used for the template-name and it is empty
		// or unset it will be the default value if we use '' and the original template
		// gets parsed again which ends in recursion.
		$tpl = $template !== -1 ? $template : $this->_filename;
		
		// the template must not be empty!
		if(empty($tpl))
			PLIB_Helper::def_error('notempty','tpl',$tpl);
		
		// store template-name for error-message
		$old_tpl = $this->_filename;
		$this->_filename = $tpl;
		
		// do we have already cached and included the template?
		$func_name = $this->get_function_name($tpl);
		if(!function_exists($func_name))
		{
			// set template-path if not already done
			$template_path = $this->_template_path;
			if($template_path == '')
			{
				if(!self::prop_exists('user'))
					PLIB_Helper::error('The property "user" doesn\'t exist! Please set a template-path!');
				
				$tp = $this->user->get_theme_item_path('templates/'.$tpl);
				$template_path = dirname($tp).'/';
			}
			
			// check if the file exists
			if(!is_file($template_path.$tpl))
				PLIB_Helper::error('"'.$template_path.$tpl.'" is no file!');
			
			$path = str_replace(PLIB_Path::inner(),'',$template_path);
			$path = str_replace('/','_',$path);
			$path = str_replace('\\','_',$path);
			$cache_path = $this->_cache_folder.'/'.$path.$tpl.'.php';
	
			// check if we have to recompile the template
			if(!file_exists($cache_path))
				$recompile_necessary = true;
			else
			{
				$cache_mtime = filemtime($cache_path);
				$tpl_mtime = filemtime($template_path.$tpl);
				
				// compare the last-modified-times
				if($tpl_mtime > $cache_mtime)
					$recompile_necessary = true;
			}
	
			// retrieve the template-content if we have to recompile it
			if($recompile_necessary)
				$tpl_content = PLIB_FileUtils::read($template_path.$tpl);
		}
		
		// recompile?
		$include = true;
		if($recompile_necessary)
		{
			// if we could not save the template we eval the code directly
			// this prevents problems if the cache-directory has not yet CHMOD 0777 (which may happen
			// at the beginning of the installation)
			$parser = new PLIB_Template_Parser($this,$this->_filename);
			$tpl_content = $parser->compile_template($tpl,$cache_path,$tpl_content);
			if($tpl_content !== '')
			{
				// show the warning just once
				if(!$this->_showed_chmod_warning)
				{
					echo '<b><span style="color: red;">WARNUNG: Das Verzeichnis "'.dirname($cache_path)
							.'" ist nicht beschreibbar oder Dateien in diesem Verzeichnis sind nicht beschreibbar.'
							.' Bitte setzen Sie den CHMOD des Verzeichnisses auf 0777 bzw. l&ouml;schen Sie die'
							.' Dateien in diesem Verzeichnis!'
							.'</span></b><br />'."\n";
					echo '<b><span style="color: red;">WARNING: The directory "'.dirname($cache_path)
							.'" is not writable or files in this directory are not writable!'
							.' Please set the CHMOD of this directory to 0777 or delete the files in it!'
							.'</span></b><br />'."\n";
					$this->_showed_chmod_warning = true;
				}

				$include = false;
				// we don't want to eval the php start- and end-tags
				eval(PLIB_String::substr($tpl_content,5,PLIB_String::strlen($tpl_content) - 2));
			}
		}

		if($include && isset($cache_path))
		{
			// include the cached file (just once)
			include_once($cache_path);
		}
		
		// detect recursions
		$tplc = count($this->_tpl_calls);
		if($tplc > 0 && $this->_tpl_calls[$tplc - 1] == $tpl)
			die('It seems that the template "'.$tpl.'" includes itself :)');
		
		array_push($this->_tpl_calls,$tpl);

		// call the function with corresponding part-argument
		$func_name = $this->get_function_name($tpl);
		$str = $func_name($this,$number);
		
		array_pop($this->_tpl_calls);
		
		// restore old name
		$this->_filename = $old_tpl;
		
		if($restore)
			$this->restore_template();

		return $str;
	}
	
	/**
	 * @param string $template the template to use
	 * @return string the function-name for the given template which will be used
	 */
	public function get_function_name($template)
	{
		return 'PLIB_TPL_'.md5(PLIB_Path::inner()).'_'.str_replace('.','_',$template);
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>