<?php
/**
 * Contains the document-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This should be the base class for all pages that can be displayed in the browser.
 * A page is a complete HTML-page which is send to the browser. That means the page
 * controls everything which is send to the browser. For example it may add
 * a header and footer, show a module in the main-area and use output-buffering.
 * <br>
 * Note that you have to call <var>_finish()</var> before the script ends because
 * this method performs actions like closing the db-connection, writing session-data
 * to the storage and so on.
 * <br>
 * This class loads all objects which are stored in every {@link PLIB_FullObject}
 * of the library. You may change the classes that are used.
 * <br>
 * Additionally it contains a timer to measure the time for the script and you may set
 * errors to the document
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @see _finish()
 * @see _start_document()
 * @see _send_document()
 * @see redirect()
 */


abstract class PLIB_Document extends PLIB_FullObject
{
	/**
	 * The timer to measure the taken time for the script
	 *
	 * @var PLIB_Timer
	 */
	private $_timer;

	/**
	 * The result of the action in this run.
	 *
	 * @see perform_actions()
	 * @var integer
	 */
	private $_action_result;
	
	/**
	 * Redirect information:
	 * <pre>
	 * 	array(
	 * 		'url' => &lt;URL&gt;,
	 * 		'time' => &lt;wait_time&gt;
	 *	)
	 * </pre>
	 * Contains false if no redirect is required
	 *
	 * @var mixed
	 */
	private $_redirect = false;

	/**
	 * The action-performer-object
	 *
	 * @var PLIB_Actions_Performer
	 */
	protected $_action_perf;
	
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_init();
	}
	
	/**
	 * @return int the taken time
	 */
	public final function get_script_time()
	{
		return $this->_timer->stop();
	}
	
	/**
	 * Adds the given action to the action-performer.
	 *
	 * @param PLIB_Actions_Base $action an instance of an inherited class of {@link PLIB_Actions_Base}
	 * @see load_module_action()
	 */
	public final function add_action($action)
	{
		$this->_action_perf->add_action($action);
	}
	
	/**
	 * Loads the action-class in the given module with given name and returns it
	 *
	 * @param int $id the id of the action
	 * @param string $module the module-name
	 * @param string $name the name of the action
	 * @param string $folder the folder of the modules (starting at PLIB_Path::inner())
	 * @return PLIB_Actions_Base the action or null if an error occurred
	 * @see add_action()
	 */
	public final function load_module_action($id,$module,$name,$folder)
	{
		PLIB_FileUtils::ensure_trailing_slash($folder);
		$cfolder = PLIB_Path::inner().$folder.$module;
		if(!is_dir($cfolder))
			PLIB_Helper::error('"'.$cfolder.'" is no folder!');
		if(empty($name))
			PLIB_Helper::def_error('notempty','name',$name);
		
		$file = $cfolder.'/action_'.$name.'.php';
		if(is_file($file))
		{
			$prefix = $this->_action_perf->get_prefix();
			include_once($file);
			$classname = $prefix.$module.'_'.$name;
			if(class_exists($classname))
				return new $classname($id);
		}
		else
			PLIB_Helper::error('"'.$file.'" is no file!');
		
		return null;
	}

	/**
	 * Performs the necessary action
	 * You will find the result in get_action_result()
	 */
	public final function perform_actions()
	{
		$this->_action_result = $this->_action_perf->perform_actions();
	}

	/**
	 * @return the result of the action in this run. This is:
	 * <pre>
	 * 	-1 = error
	 * 	 0 = success / nothing done
	 * 	 1 = success + status-page
	 * </pre>
	 */
	public final function get_action_result()
	{
		return $this->_action_result;
	}
	
	/**
	 * @return mixed information about a redirect:
	 * <pre>
	 * 	array(
	 * 		'url' => &lt;URL&gt;,
	 * 		'time' => &lt;wait_time&gt;
	 *	)
	 * </pre>
	 * or false if no redirect has been requested.
	 */
	public final function get_redirect()
	{
		return $this->_redirect;
	}
	
	/**
	 * Requests a redirect via meta-tag to the given URL after the given time (in seconds).
	 *
	 * @param string $url the target-URL
	 * @param int $time the number of seconds to wait
	 */
	public final function request_redirect($url,$time = 3)
	{
		$this->_redirect = array(
			'url' => $url,
			'time' => $time
		);
	}
	
	/**
	 * Redirects the user to the given URL. Takes care of IIS and other stuff.
	 * Will immediately quit the current script!
	 *
	 * @param string $url the URL where you want to redirect to
	 * 	Note that you have to start with {@link PLIB_Path::inner()}! (or http://)
	 */
	public final function redirect($url)
	{
		if(empty($url))
			PLIB_Helper::def_error('notempty','url',$url);

		$this->_finish();

		header("Connection: close");
		header("HTTP/1.1 303 REDIRECT");

		if(!PLIB_String::starts_with($url,'http://'))
		{
			$parts = explode('/',PLIB_Path::outer());
			$path = '';
			for($i = 0;$i < 3;$i++)
				$path .= $parts[$i] . '/';

			if(PLIB_String::starts_with($url,'/'))
				$url = PLIB_String::substr($url,1);

			$url = $path.$url;
		}

		header('Location: '.$url);
		exit;
	}

	/**
	 * Should include, instantiate and return the action-performer-object.
	 * You may overwrite this method to change the behaviour
	 *
	 * @return PLIB_Actions_Performer the action-performer
	 */
	protected function _load_action_perf()
	{
		$c = new PLIB_Actions_Performer();
		return $c;
	}

	/**
	 * Should include, instantiate and return the database-object.
	 * You have to overwrite this method because you have to specify
	 * the db-connection-data.
	 * By default the db-class is <lib>/mysql.php
	 *
	 * @return PLIB_MySQL the db-object
	 */
	protected abstract function _load_db();

	/**
	 * Should include, instantiate and return the messages-object.
	 * You have to overwrite this method!
	 *
	 * @return PLIB_Messages the messages-object
	 */
	protected abstract function _load_msgs();

	/**
	 * Should include, instantiate and return the session-manager-object.
	 * You may overwrite this method.
	 * By default it does not allow logins.
	 *
	 * @return PLIB_Session_Manager the session-manager-object
	 */
	protected function _load_sessions()
	{
	  $storage = new PLIB_Session_Storage_Empty();
	  $c = new PLIB_Session_Manager($storage,false);
	  return $c;
	}
	
	/**
	 * Should include, instantiate and return the current-user-object
	 * You may overwrite this method.
	 * By default it does not allow logins.
	 *
	 * @return PLIB_User_Current the current-user-object
	 */
	protected function _load_user()
	{
	  $c = new PLIB_User_Current(null,false);
	  return $c;
	}

	/**
	 * Should include, instantiate and return the cookies-object.
	 * You may overwrite this method.
	 * By default the cookies-class is <lib>/cookies.php
	 *
	 * @return PLIB_Cookies the cookies-object
	 */
	protected function _load_cookies()
	{
		$c = new PLIB_Cookies('plib_');
		return $c;
	}

	/**
	 * Should include, instantiate and return the input-object.
	 * You may overwrite this method.
	 * By default the input-class is <lib>/input.php
	 *
	 * @return PLIB_Input the input-object
	 */
	protected function _load_input()
	{
		$c = PLIB_Input::get_instance();
		return $c;
	}

	/**
	 * Should include, instantiate and return the URL-object.
	 * You may overwrite this method.
	 * By default the URL-class is <lib>/url.php
	 *
	 * @return PLIB_URL the URL-object
	 */
	protected function _load_url()
	{
		$c = new PLIB_URL();
		return $c;
	}

	/**
	 * Should include, instantiate and return the template-object.
	 * You may overwrite this method.
	 * By default the template-class is <lib>/template.php
	 *
	 * @return PLIB_Template_Handler the template-object
	 */
	protected function _load_tpl()
	{
		$c = new PLIB_Template_Handler();
		return $c;
	}

	/**
	 * Should include, instantiate and return the locale-object.
	 * You may overwrite this method.
	 * By default the class is <lib>/locale/en.php
	 *
	 * @return PLIB_Locale the html-object
	 */
	protected function _load_locale()
	{
		$c = new PLIB_Locale_EN();
		return $c;
	}

	/**
	 * Inits all necessary stuff
	 */
	protected final function _init()
	{
		// start timer
		$this->_timer = new PLIB_Timer();

		PLIB_Object::set_prop('doc',$this);
		
		// at first we load the properties
		$deplist = $this->_get_const_dependency_list();
		$this->_load_properties($deplist,'const');
		
		$this->_action_perf = $this->_load_action_perf();
		
		// now init all properties
		$deplist = $this->_get_init_dependency_list();
		$this->_load_properties($deplist,'init');
	}
	
	/**
	 * Should return an array with the properties and their dependencies. This list
	 * describes in what order the properties are constructed. Note that for every
	 * property the method <var>_load_<propName>()</var> has to exist and return
	 * the instance of the property!
	 * By default this method returns the list that the library requires to run.
	 * <br>
	 * The format is:
	 * <code>
	 * 	array(
	 * 		'<propName>' => array('<dep1>,...,<depN>),
	 * 		...
	 * 	)
	 * </code>
	 * <br>
	 * Please take care that a property doesn't depend on itself. For example:
	 * <code>
	 * 	array(
	 * 		'p1' => array('p2'),
	 * 		'p2' => array('p1')
	 * 	)
	 * </code>
	 *
	 * @return array the dependency list
	 * @see _get_init_dependency_list()
	 */
	protected function _get_const_dependency_list()
	{
		return array(
			'db' => array(),
			'input' => array(),
			'url' => array(),
			'cookies' => array(),
			'tpl' => array(),
			'sessions' => array(),
			'user' => array('sessions'),
			'locale' => array(),
			'msgs' => array()
		);
	}
	
	/**
	 * Should return an array with the properties and their dependencies. This list
	 * describes in what order the properties are initialized. Note that the listed
	 * properties have to implement {@link PLIB_Initable}!
	 * <br>
	 * The format is:
	 * <code>
	 * 	array(
	 * 		'<propName>' => array('<dep1>,...,<depN>),
	 * 		...
	 * 	)
	 * </code>
	 * 
	 * @return array the dependency list
	 * @see _get_const_dependency_list()
	 */
	protected function _get_init_dependency_list()
	{
		return array(
			'user' => array(),
			'sessions' => array()
		);
	}
	
	/**
	 * Loads all properties recursivly corresponding to the specified dependencies.
	 * 
	 * @param array $deplist the dependency-list
	 * @param string $type the type of operation: const or init
	 */
	private function _load_properties($deplist,$type = 'const')
	{
		$loaded = array();
		foreach(array_keys($deplist) as $prop)
		{
			// just load the property if it is not already loaded
			if(!isset($loaded[$prop]))
				$this->_load_prop($loaded,$deplist,$prop,$type);
		}
	}
	
	/**
	 * Loads the given property and marks it as loaded in the given array.
	 * Will also load all dependencies of this property.
	 *
	 * @param array $loaded the array with all loaded properties
	 * @param array $deplist the dependency-list
	 * @param string $name the name of the property
	 * @param string $type the type of operation: const or init
	 */
	private function _load_prop(&$loaded,$deplist,$name,$type = 'const')
	{
		// at first we have to load all dependencies
		foreach($deplist[$name] as $depname)
		{
			if(!isset($loaded[$depname]))
				$this->_load_prop($loaded,$deplist,$depname,$type);
		}
		
		// now we can load the property
		if($type == 'const')
		{
			$method = '_load_'.$name;
			PLIB_Object::set_prop($name,$this->$method());
		}
		else
		{
			$prop = PLIB_Object::get_prop($name);
			if($prop instanceof PLIB_Initable)
				$prop->init();
		}
		$loaded[$name] = true;
	}
	
	/**
	 * Finishes the page. Closes the database-connection and other things
	 *
	 */
	protected function _finish()
	{
		if(self::prop_exists('sessions') && $this->sessions instanceof PLIB_Session_Manager)
			$this->sessions->finalize();

		if(self::prop_exists('db') && $this->db instanceof PLIB_MySQL)
			$this->db->disconnect();
	}

	/**
	 * starts the document (starts the output-buffer)
	 *
	 * @param boolean $use_gzip do you want to compress the output?
	 */
	protected function _start_document($use_gzip = true)
	{
		if(!headers_sent())
		{
			// don't nest the output-buffers if we're using gzip
			if($use_gzip && (!function_exists('ob_get_level') || ob_get_level() <= 1))
				@ob_start('ob_gzhandler');
			else
				@ob_start();
		}
	}

	/**
	 * sends the document (flushes the output-buffer)
	 *
	 * @param boolean $use_gzip do you want to compress the output?
	 */
	protected function _send_document($use_gzip = true)
	{
		if(!headers_sent())
		{
			@header('Cache-Control: no-cache, must-revalidate, max-age=0');
			if($use_gzip && (!function_exists('ob_get_level') || ob_get_level() <= 2))
			{
				if($encoding = $this->_check_gzip())
				{
					$gzip_contents = ob_get_contents();
					ob_end_clean();
	
					header('Content-Encoding: '.$encoding);
					$gzip_size = strlen($gzip_contents);
					$gzip_crc = crc32($gzip_contents);
					$gzip_contents = gzcompress($gzip_contents,3);
					// note that we can't use PLIB_String (with multibyte enabled) here
					$gzip_contents = substr($gzip_contents,0,-4);
	
					echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
					echo $gzip_contents;
					echo pack('V',$gzip_crc);
					echo pack('V',$gzip_size);
				}
				else
					ob_end_flush();
			}
			else
				ob_end_flush();
		}
	}

	/**
	 * checks wether the client accepts gzip
	 *
	 * @return boolean true if the client supports gzip
	 */
	private function _check_gzip()
	{
		$encoding = $this->input->get_var('HTTP_ACCEPT_ENCODING','server',PLIB_Input::STRING);
		if($encoding === null)
	    return 0;

		if(PLIB_String::strpos($encoding,'x-gzip') !== false)
			return 'x-gzip';
		if(PLIB_String::strpos($encoding,'gzip') !== false)
			return 'gzip';

		return 0;
	}
}
?>