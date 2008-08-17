<?php
/**
 * Contains the URL-class
 *
 * @version			$Id: url.php 28 2008-07-30 19:24:34Z nasmussen $
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This class makes it easier to create an URL. It appends automaticly the session-id,
 * if necessary, appends the external variables and so on.
 *
 * @package			FrameWorkSolution
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_URL extends FWS_Object
{
	/**
	 * Will always append the session-id
	 */
	const SID_FORCE = 0;
	
	/**
	 * Will append the session-id if the user has disabled cookies
	 */
	const SID_AUTO = 1;
	
	/**
	 * Will never append the session-id
	 */
	const SID_OFF = 2;
	
	/**
	 * Contains the path and file of $_SERVER['PHP_SELF']
	 *
	 * @var array
	 */
	private static $_phpself = null;
	
	/**
	 * The session-id which will be appended
	 *
	 * @var mixed
	 */
	private static $_session_id = -1;

	/**
	 * The external variables (as string)
	 *
	 * @var string
	 */
	private static $_extern_vars = '';

	/**
	 * Do you want to append extern get-parameter?
	 *
	 * @var boolean
	 */
	private static $_append_extern = true;

	/**
	 * @return boolean wether extern parameters will be appended
	 */
	public static function append_extern_vars()
	{
		return self::$_append_extern;
	}

	/**
	 * Sets wether to append the extern parameters (of other projects)
	 * Please note that you have to overwrite the is_intern() method!
	 *
	 * @param boolean $append the new value
	 */
	public static function set_append_extern_vars($append)
	{
		self::$_append_extern = $append;
	}

	/**
	 * @return array an array of the form <code>array(<param_name>,<session_id>)</code> or false
	 */
	public static function get_session_id()
	{
		if(self::$_session_id !== false)
		{
			$user = FWS_Props::get()->user();
			return array($user->get_url_sid_name(),$user->get_session_id());
		}

		return false;
	}

	/**
	 * Is the session-id used?
	 *
	 * @return boolean true if the session-id is used
	 */
	public static function needs_session_id()
	{
		return self::$_session_id !== false;
	}
	
	/**
	 * The session-id-policy
	 *
	 * @var int
	 */
	private $_sid_policy = self::SID_AUTO;
	
	/**
	 * The separator for the parameters
	 *
	 * @var string
	 */
	private $_separator = '&amp;';
	
	/**
	 * Wether the URL should be absolute
	 *
	 * @var boolean
	 */
	private $_absolute = false;
	
	/**
	 * The path that should be used (null = current one)
	 *
	 * @var string
	 */
	private $_path = null;
	
	/**
	 * The file that should be used (null = current one)
	 *
	 * @var string
	 */
	private $_file = null;
	
	/**
	 * The anchor that should be appended
	 *
	 * @var string
	 */
	private $_anchor = null;
	
	/**
	 * The parameter for the URL
	 *
	 * @var array
	 */
	private $_params = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		if(self::$_phpself === null)
			$this->init();
	}

	/**
	 * Checks wether the given parameter is intern (belongs to the project).
	 * By default the method returns true so that no parameter will be
	 * appended automatically.
	 * This may be usefull if you want to support including your project into
	 * another one. In this case it makes sense to append all unknown get-parameter
	 * (of the other project) to the URL.
	 * Please overwrite this method to support that.
	 *
	 * @param string $param the name of the parameter
	 * @return boolean true if it belongs to Boardsolution
	 */
	public function is_intern($param)
	{
		return true;
	}

	/**
	 * @return array an associative array with the external variables
	 */
	public final function get_extern_vars()
	{
		$input = FWS_Props::get()->input();

		$vars = array();
		foreach($input->get_vars_from_method('get') as $key => $value)
		{
			if(!$this->is_intern($key))
				$vars[$key] = $value;
		}
		return $vars;
	}

	/**
	 * @return string the file that should be used (null = current one)
	 */
	public final function get_file()
	{
		return $this->_file;
	}

	/**
	 * Sets the file that should be used (null = current one)
	 * 
	 * @param string $file the new value
	 * @see set_path()
	 */
	public final function set_file($file)
	{
		if($file !== null && !is_string($file))
			FWS_Helper::def_error('string','file',$file);
		
		$this->_file = $file;
	}

	/**
	 * @return string the path (including protocol and so on, if needed) to use (null = current one)
	 */
	public final function get_path()
	{
		return $this->_path;
	}

	/**
	 * Sets the path (including protocol and so on, if needed) to use (null = current one)
	 * 
	 * @param string $path the new value
	 * @see set_file()
	 */
	public final function set_path($path)
	{
		if($path !== null && !is_string($path))
			FWS_Helper::def_error('string','path',$path);
		
		if($path == '')
			$this->_path = '';
		else
			$this->_path = FWS_FileUtils::ensure_trailing_slash($path);
	}
	
	/**
	 * @return int the session-id-policy (self::SID_FORCE, self::SID_AUTO, self::SID_OFF)
	 */
	public final function get_sid_policy()
	{
		return $this->_sid_policy;
	}
	
	/**
	 * Sets the session-id-policy
	 *
	 * @param int $policy the session-id-policy (self::SID_FORCE, self::SID_AUTO, self::SID_OFF)
	 */
	public final function set_sid_policy($policy)
	{
		$vals = array(self::SID_FORCE,self::SID_AUTO,self::SID_OFF);
		if(!in_array($policy,$vals))
			FWS_Helper::def_error('inarray','policy',$vals,$policy);
		
		$this->_sid_policy = $policy;
	}
	
	/**
	 * @return string the separator for the parameters
	 */
	public final function get_separator()
	{
		return $this->_separator;
	}
	
	/**
	 * Sets the separator for the parameters
	 *
	 * @param string $sep the new value
	 */
	public final function set_separator($sep)
	{
		if(!is_string($sep))
			FWS_Helper::def_error('string','sep',$sep);
		
		$this->_separator = $sep;
	}
	
	/**
	 * @return boolean true if an absolute URL will be generated
	 */
	public final function is_absolute()
	{
		return $this->_absolute;
	}
	
	/**
	 * Sets wether an absolute URL should be generated
	 *
	 * @param boolean $abs the new value
	 */
	public final function set_absolute($abs)
	{
		$this->_absolute = $abs ? true : false;
	}
	
	/**
	 * @return string the anchor that will be appended to the URL (null if no exists)
	 */
	public final function get_anchor()
	{
		return $this->_anchor;
	}
	
	/**
	 * Sets the anchor that should be appended to the URL
	 *
	 * @param string $anchor the new value (without # and not urlencoded!)
	 */
	public final function set_anchor($anchor)
	{
		$this->_anchor = $anchor;
	}
	
	/**
	 * Returns the value of the given parameter (not urlencoded!)
	 *
	 * @param string $name the name of the parameter
	 * @return mixed the value of it or <var>null</var> if it doesn't exist
	 */
	public final function get($name)
	{
		if(!isset($this->_params[$name]))
			return null;
		
		return $this->_params[$name];
	}
	
	/**
	 * Sets the parameter with given name to given value
	 *
	 * @param string $name the name of the parameter
	 * @param mixed $value the value of the parameter (not urlencoded!). May also be an array.
	 * @return FWS_URL this instance
	 */
	public final function set($name,$value)
	{
		$this->_params[$name] = $value;
		return $this;
	}
	
	/**
	 * Copies the values of the given parameters from the given URL to this one
	 *
	 * @param FWS_URL $url the URL-instance
	 * @param array $params an array of parameter-names
	 */
	public final function copy_params($url,$params)
	{
		foreach($params as $name)
			$this->_params[$name] = $url->get($name);
	}
	
	/**
	 * Removes the parameter with given name from the parameters
	 *
	 * @param string $name the name of the parameter
	 * @return FWS_URL this instance
	 */
	public final function remove($name)
	{
		unset($this->_params[$name]);
		return $this;
	}
	
	/**
	 * Builds the URL with the specified properties and returns it
	 *
	 * @return string the URL
	 */
	public function to_url()
	{
		$url = '';
		
		// append path
		if($this->_path !== null)
			$url .= $this->_path;
		else
		{
			if($this->_absolute)
				$url .= FWS_Path::outer();
			else
				$url .= self::$_phpself[0];
		}
		
		// append file
		if($this->_file !== null)
			$url .= $this->_file;
		else
			$url .= self::$_phpself[1];
		
		// append extern and sid
		$params = $this->_params;
		if(self::$_append_extern)
			$params = array_merge($this->_params,self::$_extern_vars);
		
		if($this->_sid_policy == self::SID_FORCE)
		{
			$sid = $this->get_session_param(true);
			if($sid !== false)
				$params[$sid[0]] = $sid[1];
		}
		else if($this->_sid_policy != self::SID_OFF && self::$_session_id !== false)
			$params[self::$_session_id[0]] = self::$_session_id[1];
		
		// append params
		if(count($params) > 0)
		{
			$url .= '?';
			foreach($params as $k => $v)
			{
				if(is_array($v))
				{
					$ek = urlencode($k.'[]');
					foreach($v as $vv)
						$url .= $ek.'='.$vv.$this->_separator;
				}
				else
					$url .= urlencode($k).'='.urlencode($v).$this->_separator;
			}
			$url = FWS_String::substr($url,0,-FWS_String::strlen($this->_separator));
		}
		
		// append anchor
		if($this->_anchor !== null)
			$url .= '#'.urlencode($this->_anchor);
		
		return $url;
	}

	/**
	 * initializes everything
	 */
	protected function init()
	{
		$input = FWS_Props::get()->input();
		
		$phpself = $input->get_var('PHP_SELF','server',FWS_Input::STRING);
		self::$_phpself = array(dirname($phpself).'/',basename($phpself));
		
		// init the extern-variables
		self::$_extern_vars = array();
		if(self::$_append_extern)
		{
			foreach($input->get_vars_from_method('get') as $key => $value)
			{
				if(!$this->is_intern($key))
					self::$_extern_vars[$key] = $value;
			}
		}
		
		self::$_session_id = $this->get_session_param();
	}
	
	/**
	 * Returns the session-parameter-data
	 *
	 * @param boolean $force wether you want to force a session-id
	 * @return mixed an array with the key and value or false if no sid should be used
	 */
	protected function get_session_param($force = false)
	{
		$cookies = FWS_Props::get()->cookies();
		$user = FWS_Props::get()->user();
		
		// can we find the cookie?
		$use_sid = $force;

		// NOTE: we don't use $input here because we always set the cookies in that class!
		// so we will always find them there, no matter if the user has activated cookies or not
		if(!$use_sid && isset($_COOKIE))
			$use_sid = !isset($_COOKIE[$cookies->get_prefix().'sid']);

		if($use_sid && $user instanceof FWS_User_Current)
		{
			$sid = $user->get_session_id();
			if($sid)
				return array($user->get_url_sid_name(),$sid);
			return false;
		}
		
		return false;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>