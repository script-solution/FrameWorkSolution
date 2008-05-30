<?php
/**
 * Contains the URL-class
 *
 * @version			$Id: url.php 744 2008-05-24 15:11:18Z nasmussen $
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This class makes it easier to create an URL. It appends automaticly the session-id,
 * if necessary, appends the external variables and so on.
 * Please note that some variables are lazy initialized. So please call $this->_init() to do
 * that if you extend this class and implement additional methods.
 * 
 * TODO what to finalize here?
 *
 * @package			PHPLib
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_URL extends PLIB_FullObject
{
	/**
	 * The session-id which will be appended
	 *
	 * @var mixed
	 */
	protected $_session_id = -1;

	/**
	 * The external variables (as string)
	 *
	 * @var string
	 */
	protected $_extern_vars = '';

	/**
	 * Do you want to append extern get-parameter?
	 *
	 * @var boolean
	 */
	protected $_append_extern = true;

	/**
	 * The name of the action-get-parameter
	 *
	 * @var string
	 */
	protected $_action_param = 'action';
	
	/**
	 * The prefix for the URL-constants
	 *
	 * @var string
	 */
	protected $_url_constants_prefix = '';
	
	/**
	 * The cached $_SERVER['PHP_SELF']
	 *
	 * @var string
	 */
	private $_phpself = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_phpself = $this->input->get_var('PHP_SELF','server',PLIB_Input::STRING);
	}

	/**
	 * @return string the action-parameter-name
	 */
	public function get_action_param()
	{
		return $this->_action_param;
	}

	/**
	 * Sets the action-parameter-name
	 *
	 * @param string $name the new value
	 */
	public function set_action_param($name)
	{
		$this->_action_param = $name;
	}

	/**
	 * @return boolean wether extern variables will be appended
	 */
	public function append_extern_vars()
	{
		return $this->_append_extern;
	}

	/**
	 * Sets wether to append the external variables.
	 * Please note that you have to overwrite the is_intern() method!
	 *
	 * @param boolean $append the new value
	 */
	public function set_append_extern_vars($append)
	{
		$this->_append_extern = $append;
	}
	
	/**
	 * @return string the prefix for the constants
	 */
	public function get_constants_prefix()
	{
		return $this->_constants_prefix;
	}
	
	/**
	 * Sets the prefix for the constants. For example "BS_" if your constants are named
	 * like "BS_<name>". The prefix may also be empty.
	 * 
	 * @param string $prefix the new value
	 */
	public function set_constants_prefix($prefix)
	{
		$this->_constants_prefix = $prefix;
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
	public function get_extern_vars()
	{
		$vars = array();
		foreach($this->input->get_vars_from_method('get') as $key => $value)
		{
			if(!$this->is_intern($key))
				$vars[$key] = $value;
		}
		return $vars;
	}

	/**
	 * Builds an URL for the given file (for example for the inc-directory)
	 * Note that this method does NOT append the external vars. Therefore you should
	 * always create a link to a standalone-file!
	 *
	 * @param string $file the file (starting at {@link PLIB_Path::inner()})
	 * @param string $additional additional parameters
	 * @param string $separator the separator for the parameters (default = &amp;)
	 * @param boolean $absolute use the absolute URL or {@link PLIB_Path::inner()}?
	 * @return string the url
	 */
	public function get_file_url($file,$additional = '',$separator = '&amp;',$absolute = false)
	{
		$this->_init();

		// Note that we don't append the external vars here because this leads always to
		// a standalone file!
		$parameters = '';
		if($separator == '&')
			$parameters .= str_replace('&amp;','&',$this->_session_id);
		else
			$parameters .= $this->_session_id;
		$parameters .= $additional;

		$first_sep = PLIB_String::strpos($file,'?') !== false ? $separator : '?';
		$base = $absolute ? PLIB_Path::outer() : PLIB_Path::inner();
		if($parameters == '')
			$url = $base.$file;
		else if($separator == '&' && $parameters[0] == $separator)
			$url = $base.$file.$first_sep.PLIB_String::substr($parameters,1);
		else if($separator == '&amp;' && PLIB_String::substr($parameters,0,5) == '&amp;')
			$url = $base.$file.$first_sep.PLIB_String::substr($parameters,5);
		else
			$url = $base.$file.$first_sep.$parameters;

		return $url;
	}
	
	/**
	 * Works the same like get_url but is mainly intended for usage in the templates.
	 * You can use the following shortcut for the constants (in <var>$additional</var>):
	 * <code>$<name></code>
	 * This will be mapped to the constant:
	 * <code><constants_prefix><name></code>
	 * Note that the constants will be assumed to be in uppercase!
	 * 
	 * @param string $target the action-parameter (0 = current, -1 = none)
	 * @param string $additional additional parameters
	 * @param string $separator the separator of the params (default is &amp;)
	 * @param boolean $force_sid forces the method to append the session-id
	 * @return string the url
	 */
	public function simple_url($target = 0,$additional = '',$separator = '&amp;',$force_sid = false)
	{
		if($additional != '')
		{
			$additional = preg_replace(
				'/\$([a-z0-9_]+)/ie',
				$this->_constants_prefix.'\\1',
				$additional
			);
		}
		return $this->get_url($target,$additional,$separator,$force_sid);
	}

	/**
	 * The main method. This generates an URL with given parameters and returns it.
	 * The extern-variables (if you want it) and the session-id (if necessary)
	 * will be appended.
	 * The file will be <var>$_SERVER['PHP_SELF']</var>.
	 *
	 * @param string $target the action-parameter (0 = current, -1 = none)
	 * @param string $additional additional parameters
	 * @param string $seperator the separator of the params (default is &amp;)
	 * @param boolean $force_sid forces the method to append the session-id
	 * @return string the url
	 */
	public function get_url($target = 0,$additional = '',$separator = '&amp;',$force_sid = false)
	{
		$this->_init();

		if($target === -1)
			$action = '';
		else if($target === 0)
		{
			$action_param = $this->input->get_var($this->_action_param,'get',PLIB_Input::STRING);
			if($action_param == null)
				$action = '';
			else
				$action = $this->_action_param.'='.$action_param;
		}
		else
			$action = $this->_action_param.'='.$target;

		$parameters = $action;
		if($separator == '&')
		{
			if($force_sid)
				$parameters .= '&'.$this->user->get_url_sid_name().'='.$this->user->get_session_id();
			else
				$parameters .= str_replace('&amp;','&',$this->_session_id);
			$parameters .= str_replace('&amp;','&',$this->_extern_vars);
		}
		else
		{
			if($force_sid)
				$parameters .= '&amp;'.$this->user->get_url_sid_name().'='.$this->user->get_session_id();
			else
				$parameters .= $this->_session_id;
			$parameters .= $this->_extern_vars;
		}
		$parameters .= $additional;

		if($parameters == '')
			$url = $this->_phpself;
		else if($separator == '&' && $parameters[0] == $separator)
			$url = $this->_phpself.'?'.PLIB_String::substr($parameters,1);
		else if($separator == '&amp;' && PLIB_String::substr($parameters,0,5) == '&amp;')
			$url = $this->_phpself.'?'.PLIB_String::substr($parameters,5);
		else
			$url = $this->_phpself.'?'.$parameters;

		return $url;
	}

	/**
	 * Is the session-id used?
	 *
	 * @return boolean true if the session-id is used
	 */
	public function use_session_id()
	{
		$this->_init();

		return $this->_session_id != '';
	}

	/**
	 * initializes everything
	 *
	 */
	protected function _init()
	{
		if($this->_session_id !== -1)
			return;

		// init the extern-variables
		$this->_extern_vars = '';
		if($this->_append_extern)
		{
			foreach($this->input->get_vars_from_method('get') as $key => $value)
			{
				if(!$this->is_intern($key))
					$this->_extern_vars .= '&amp;'.$key.'='.$value;
			}
		}

		// can we find the cookie?
		$use_sid = false;

		// NOTE: we don't use $this->input here because we always set the cookies in that class!
		// so we will always find them there, no matter if the user has activated cookies or not
		if(isset($_COOKIE))
			$use_sid = !isset($_COOKIE[$this->cookies->get_prefix().'sid']);
		else
		{
			global $HTTP_COOKIE_VARS;
			$use_sid = !isset($HTTP_COOKIE_VARS[$this->cookies->get_prefix().'sid']);
		}

		if($this->user instanceof PLIB_User_Current && $use_sid)
		{
			$sid = $this->user->get_session_id();
			if($sid)
				$this->_session_id = '&amp;'.$this->user->get_url_sid_name().'='.$sid;
			else
				$this->_session_id = '';
		}
		else
			$this->_session_id = '';
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>