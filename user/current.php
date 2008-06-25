<?php
/**
 * Contains the class which represents the current user
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	user
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents the current user. Contains a {@link PLIB_Session_Data} object and some
 * more information for the current user. It manages the login-state and some
 * other stuff.
 *
 * @package			PHPLib
 * @subpackage	user
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_User_Current extends PLIB_FullObject implements PLIB_Initable
{
	/**
	 * No error in the login-procedure
	 */
	const LOGIN_ERROR_NO_ERROR								= -1;
	
	/**
	 * The user has not been found
	 */
	const LOGIN_ERROR_USER_NOT_FOUND					= 0;
	
	/**
	 * The username is incorrect
	 */
	const LOGIN_ERROR_USER_NAME_INCORRECT			= 1;
	
	/**
	 * The password is incorrect
	 */
	const LOGIN_ERROR_PW_INCORRECT						= 2;
	
	/**
	 * The data of the user
	 *
	 * @var PLIB_Session_Data
	 */
 	protected $_user;
	
	/**
	 * The data of this user (null if it is a guest)
	 *
	 * @var PLIB_User_Data
	 */
	protected $_userdata = null;
	
	/**
	 * The storage-object for the userdata
	 *
	 * @var PLIB_User_Storage
	 */
	protected $_storage;
	
	/**
	 * An array with data to store in the session (additional to the basic data)
	 *
	 * @var array
	 */
	private $_session_data = array();
	
	/**
	 * Use cookies to login?
	 *
	 * @var boolean
	 */
	private $_use_cookies = true;

	/**
	 * The name of the URL sid-parameter
	 *
	 * @var string
	 */
	private $_url_sid_name = 'sid';

	/**
	 * The type of the ip-validation:
	 * <ul>
	 * 	<li>A.B.C.D</li>
	 * 	<li>A.B.C</li>
	 * 	<li>A.B</li>
	 * 	<li>none</li>
	 * </ul>
	 * If the current ip is not equal to the stored one the user will be logged out
	 *
	 * @var string
	 */
	private $_ip_validation_type = 'A.B.C.D';

	/**
	 * Do you want to check the user-agent?
	 * If enabled a user will be logged out if the current user-agent is
	 * not equal to the one stored in the db
	 *
	 * @var string
	 */
	private $_user_agent_validation = false;
	
	/**
	 * The selected theme
	 *
	 * @var string
	 */
	protected $_theme = '';
	
	/**
	 * Constructor
	 * 
	 * @param PLIB_User_Storage $storage the storage-object to use
	 * @see initialize()
	 */
	public function __construct($storage)
	{
		parent::__construct();
		
		if($this->sessions->sessions_enabled() && !($storage instanceof PLIB_User_Storage))
			PLIB_Helper::def_error('instance','storage','PLIB_User_Storage',$storage);
		
		$this->_storage = $storage;
	}
	
	/**
	 * You have to call this method in every case. Even if you don't want to allow logins!
	 * Initializes the session
	 */
	public function init()
	{
		$this->_init_session();

		if($this->sessions->sessions_enabled())
		{
			// login by cookie?
	    if($this->_use_cookies && !$this->is_loggedin())
	    {
	    	$user = $this->cookies->get_cookie('user');
	  		$pw = $this->cookies->get_cookie('pw');
	  		if($user != null && $pw != null)
		 			$this->login($user,$pw,false);
	    }
	
	    // fill userdata if not already done
	    if($this->is_loggedin() && $this->_userdata === null)
			{
				$this->_set_userdata($this->get_user_id());
	
				// check the user
				// Note that we do this at the point because if login() has been called
				// $this->_userdata is already set and has been checked, of course.
				if($this->_check_user() != self::LOGIN_ERROR_NO_ERROR)
				{
	        $this->_assign_new_session();
	    		$this->_setup_guest();
				}
			}
		}
	}

	/**
	 * Updates the session-data to the user-object
	 */
	public function finalize()
	{
		// store current date and the serialized session-data to write it to the session-storage
		$this->_user->set_date(time());
		$this->_user->set_session_data(serialize($this->_session_data));
	}
	
	/**
	 * @return int the user-id or 0 if it is a guest
	 */
	public final function get_user_id()
	{
		return $this->_user->get_user_id();
	}
	
	/**
	 * @return string the username or an empty string if it is a guest
	 */
	public final function get_user_name()
	{
		return $this->_user->get_user_name();
	}
	
	/**
	 * @return string the ip of the current user
	 */
	public final function get_user_ip()
	{
		return $this->_user->get_user_ip();
	}
	
	/**
	 * @return string the session-id of the current user
	 */
	public final function get_session_id()
	{
		return $this->_user->get_session_id();
	}
	
	/**
	 * @return string the user-agent of the current user
	 */
	public final function get_user_agent()
	{
		return $this->_user->get_user_agent();
	}
	
	/**
	 * @return string the name of the language-folder that is used
	 */
	public function get_language()
	{
		// use english by default
		return 'en';
	}
	
	/**
	 * @return string the name of the selected theme
	 */
	public final function get_theme()
	{
		return $this->_theme;
	}
	
	/**
	 * Sets the theme to given value
	 *
	 * @param string $theme the new value
	 */
	public final function set_theme($theme)
	{
		if(!is_string($theme))
			PLIB_Helper::def_error('string','theme',$theme);
		
		$this->_theme = $theme;
	}

	/**
	 * Returns the path to the given item. This allows you to provide different
	 * themes. If the theme is empty it returns:
	 * <code>PLIB_Path::inner().'theme/'.$item</code>
	 * otherwise:
	 * <code>PLIB_Path::inner().'themes/'.$this->_theme.'/'.$item</code>
	 * You may change this by overwriting this method!
	 *
	 * @param string $item the path to the item starting at a theme-folder
	 * @return string the complete path to the item (starting at the root-folder)
	 */
	public function get_theme_item_path($item)
	{
		if(empty($this->_theme))
			return PLIB_Path::inner().'theme/'.$item;
		
		return PLIB_Path::inner().'themes/'.$this->_theme.'/'.$item;
	}

	/**
	 * @return string the type of ip-validation
	 */
	public final function get_ip_validation_type()
	{
		return $this->_ip_validation_type;
	}

	/**
	 * Sets the type of ip-validation.
	 * <ul>
	 * 	<li>A.B.C.D</li>
	 * 	<li>A.B.C</li>
	 * 	<li>A.B</li>
	 * 	<li>none</li>
	 * </ul>
	 * If the current ip is not equal to the stored one the user will be logged out
	 *
	 * @param string $type the new value
	 */
	public final function set_ip_validation_type($type)
	{
		if(!in_array($type,array('A.B.C.D','A.B.C','A.B','none')))
			PLIB_Helper::def_error('inarray','type',array('A.B.C.D','A.B.C','A.B','none'),$type);

		$this->_ip_validation_type = $type;
	}

	/**
	 * @return boolean wether the user-agent will be validated
	 */
	public final function validate_user_agent()
	{
		return $this->_user_agent_validation;
	}

	/**
	 * Sets wether the user-agent should be validated.
	 * If enabled a user will be logged out if the current user-agent is
	 * not equal to the one stored in the db
	 *
	 * @param boolean $validate the new value
	 */
	public final function set_user_agent_validation($validate)
	{
		$this->_user_agent_validation = (boolean)$validate;
	}

	/**
	 * @return boolean wether cookies are used to login
	 */
	public final function use_cookies()
	{
		return $this->_use_cookies;
	}

	/**
	 * Sets wether to use cookies to login
	 *
	 * @param boolean $use_cookies the new value
	 */
	public final function set_use_cookies($use_cookies)
	{
		$this->_use_cookies = (boolean)$use_cookies;
	}

	/**
	 * @return string the name of the URL sid-parameter
	 */
	public final function get_url_sid_name()
	{
		return $this->_url_sid_name;
	}

	/**
	 * Sets the name of the URL sid-parameter
	 *
	 * @param string $name the new value
	 */
	public final function set_url_sid_name($name)
	{
		if(empty($name))
			PLIB_Helper::def_error('notempty','name',$name);

		$this->_url_sid_name = $name;
	}

	/**
	 * @return boolean true if this user is logged in
	 */
	public final function is_loggedin()
	{
		return $this->get_user_id() > 0;
	}
	
	/**
	 * @return PLIB_User_Data the user-data-object
	 */
	public final function get_userdata()
	{
		return $this->_userdata;
	}

	/**
	 * returns the session-data with the given name
	 * or false if not found
	 *
	 * @param string $name the name of the data
	 * @return mixed the data or false if not found
	 */
	public final function get_session_data($name)
	{
		if(empty($name))
			PLIB_Helper::def_error('notempty','name',$name);

		if(isset($this->_session_data[$name]))
			return $this->_session_data[$name];

		return false;
	}

	/**
	 * sets / adds the given data to the session
	 * this data will be stored into the session-table at the end of the script
	 *
	 * @param string $name the name of the data
	 * @param mixed $value the data to store
	 */
	public final function set_session_data($name,$value)
	{
		if(empty($name))
			PLIB_Helper::def_error('notempty','name',$name);

		$this->_session_data[$name] = $value;
	}

	/**
	 * deletes the session-data with given name
	 *
	 * @param mixed $name the name of the data (may be an array to delete multiple vars)
	 */
	public final function delete_session_data($name)
	{
		if(is_array($name))
		{
			foreach($name as $n)
			{
				if(isset($this->_session_data[$n]))
					unset($this->_session_data[$n]);
			}
		}
		else if(isset($this->_session_data[$name]))
			unset($this->_session_data[$name]);
	}

	/**
	 * tries to login the current user
	 *
	 * @param string $user the entered user-name
	 * @param string $pw the entered password
	 * @param string $hashpw do you want to calculate the hash of the pw before comparing it?
	 * @return int the error-code; see PLIB_Session::LOGIN_ERROR_*
	 */
	public function login($user,$pw,$hashpw = true)
	{
		$loggedin = $this->_set_userdata(0,$user);
		
		if($hashpw)
			$pw = $this->_storage->get_hash_of_pw($pw,$this->_userdata);
		
		if($loggedin == self::LOGIN_ERROR_NO_ERROR)
		{
	    // perform stripslashes here because addslashes() has been called on the value
		  // and we want to compare it with as it is
			$user = stripslashes($user);
	
			if(empty($pw))
		    $loggedin = self::LOGIN_ERROR_PW_INCORRECT;
		  else
		  	$loggedin = $this->_check_user($user,$pw);
		}
		
		// setup user or guest
		if($loggedin == self::LOGIN_ERROR_NO_ERROR)
			$this->_setup_user($user,$pw);
		else
			$this->_setup_guest();

		return $loggedin;
	}

	/**
	 * logs the user out. will destroy the session, delete the cookies and redirect the user
	 */
	public function logout()
	{
		$this->sessions->logout_user($this->get_session_id(),$this->get_user_ip());
		
		if($this->_use_cookies)
		{
	    $this->cookies->delete_cookie('user');
	    $this->cookies->delete_cookie('pw');
		}
	}
	
	/**
	 * Sets the userdata for the given user-id or username
	 * 
	 * @param int $id the user-id
	 * @param string $user the user-name
	 * @return int the error-code or self::LOGIN_ERROR_NO_ERROR
	 */
	protected function _set_userdata($id,$user = false)
	{
		if($user === false)
			$userdata = $this->_storage->get_userdata_by_id($id);
		else
			$userdata = $this->_storage->get_userdata_by_name($user);
		
		if(!$userdata)
			return self::LOGIN_ERROR_USER_NOT_FOUND;
		
		$this->_userdata = $userdata;
		$this->_user->set_user_id($userdata->get_user_id());
		$this->_user->set_user_name($userdata->get_user_name());
		$this->_user->set_user_group($userdata->get_profile_val('user_group'));
		$this->_user->set_ghost_mode($userdata->get_profile_val('ghost_mode'));
		
		return self::LOGIN_ERROR_NO_ERROR;
	}

	/**
	 * setups the required stuff for a loggedin user
	 *
	 * @param string $user the username
	 * @param string $pw the password
	 */
	protected function _setup_user($user,$pw)
	{
		if($this->_use_cookies)
		{
	    $this->cookies->set_cookie('user',$user);
	    $this->cookies->set_cookie('pw',$pw);
		}
	}

	/**
	 * setups the required stuff for a guest
	 */
	protected function _setup_guest()
	{
		$this->_user->make_guest();
		$this->_userdata = null;
		
		// delete the cookie to ensure that the user won't try to get logged in again
		if($this->_use_cookies)
		{
			$this->cookies->delete_cookie('user');
			$this->cookies->delete_cookie('pw');
		}
	}
	
	/**
	 * checks the user. if the user is loggedin a value is invalid he/she will be logged out
	 *
	 * @param mixed $user the username
	 * @param mixed $pw the password
	 */
	protected function _check_user($user = false,$pw = false)
	{
		$loggedin = self::LOGIN_ERROR_NO_ERROR;
		
		if($this->_userdata === null)
			$loggedin = self::LOGIN_ERROR_USER_NOT_FOUND;
		else if($user && $this->_userdata->get_user_name() != $user)
			$loggedin = self::LOGIN_ERROR_USER_NAME_INCORRECT;
		else if($pw && $this->_userdata->get_user_pw() != $pw)
			$loggedin = self::LOGIN_ERROR_PW_INCORRECT;

		// perform custom checks
		if($loggedin == self::LOGIN_ERROR_NO_ERROR)
			$loggedin = $this->_storage->check_user($this->_userdata);

		return $loggedin;
	}

	/**
	 * inits the required fields from the session-table in the db
	 *
	 */
	private function _init_session()
	{
		$user_ip = $this->_determine_user_ip();
		$user_agent = $this->_determine_user_agent();

		// retrieve session-id
		$session_id = '';
		if($this->cookies->isset_cookie('sid'))
			$session_id = $this->cookies->get_cookie('sid');
		else if($this->input->isset_var($this->_url_sid_name,'get'))
			$session_id = $this->input->get_var($this->_url_sid_name,'get',PLIB_Input::STRING);

		// try to load the user from the session-manager
		$user = null;
		if(!empty($session_id))
			$user = $this->sessions->get_user($session_id,$user_ip);
		
		// check the data of the user
		if($user !== null)
		{
		  // force a new session if ip or useragent has changed
			if(!$this->_check_user_ip($user->get_user_ip(),$user_ip))
			  $user = null;
			else if(!$this->_check_user_agent($user->get_user_agent(),$user_agent))
			  $user = null;
		}
		
		// load user
		if($user !== null)
		{
			$this->_user = $user;
			
			// unserialize session-data
			$this->_session_data = unserialize($user->get_session_data());
		}
		else
		{
			$this->_session_data = array();
			$this->_user = $this->sessions->get_new_user();
		}
		
		// store current ip and user-agent
	  $this->_user->set_user_agent($user_agent);
	  $this->_user->set_user_ip($user_ip);
	  
	  // create a new session?
	  if($user === null)
	  {
		  $this->_assign_new_session();
		  $this->sessions->add_user($this->_user);
	  }
	}
	
	/**
	 * Assignes a new session to the current user
	 */
	private function _assign_new_session()
	{
		// generate new session id and store it via cookie
	  $this->_user->set_session_id($this->_generate_session_id());
		$this->cookies->set_cookie(
			'sid',$this->_user->get_session_id(),$this->sessions->get_online_timeout()
		);
	}

	/**
	 * calculates the session-id based on the ip and the user-agent
	 *
	 * @return string the session-id of this user
	 */
	private function _generate_session_id()
	{
		$rand_key = md5(uniqid(rand(),true));
		return md5($this->get_user_ip().$rand_key.$this->get_user_agent());
	}
	
	/**
	 * Determines the user-agent
	 *
	 * @return string the user-agent
	 */
	private function _determine_user_agent()
	{
		$val = $this->input->get_var('HTTP_USER_AGENT','server',PLIB_Input::STRING);
		if($val === null)
			$val = '';
		return $val;
	}

	/**
	 * Determines the ip of this user
	 *
	 * @return string the user-ip
	 */
	private function _determine_user_ip()
	{
		if($this->input->isset_var('REMOTE_ADDR','server'))
			return $this->input->get_var('REMOTE_ADDR','server',PLIB_Input::STRING);

		$ip = getenv('REMOTE_ADDR');
		$ip = htmlspecialchars($ip,ENT_QUOTES);
		$ip = !get_magic_quotes_gpc() ? addslashes($ip) : $ip;
		return $ip;
	}

	/**
	 * Checks wether <var>$old_ip</var> is equal to <var>$current_ip</var>.
	 * Wether it is "equal" depends on the ip-validation-type.
	 *
	 * @param string $old_ip the old ip
	 * @param string $current_ip the current ip
	 * @return boolean true if the ips are equal
	 */
	private function _check_user_ip($old_ip,$current_ip)
	{
		switch($this->_ip_validation_type)
		{
			case 'A.B.C.D':
				return $current_ip === $old_ip;

			case 'A.B.C';
				$current = PLIB_String::substr($current_ip,0,PLIB_String::strrpos($current_ip,'.'));
				$stored = PLIB_String::substr($old_ip,0,PLIB_String::strrpos($old_ip,'.'));
				return $current === $stored;

			case 'A.B':
				$current_parts = explode('.',$current_ip);
				$stored_parts = explode('.',$old_ip);
				return $current_parts[0] === $stored_parts[0] && $current_parts[1] === $stored_parts[1];

			default:
				return true;
		}
	}

	/**
	 * Checks wether the given user-agents are equal.
	 *
	 * @param string $old_ua the old user-agent
	 * @param string $current_ua the current user-agent
	 * @return boolean true if it is equal
	 */
	private function _check_user_agent($old_ua,$current_ua)
	{
		if(!$this->_user_agent_validation)
			return true;

		// The browser sends a HEAD-request to the root-folder if the user has requested a page
		// which contains the java-applet. In this request the user-agent is different which
		// causes a logout if the user-agent-check is enabled.
		if($this->input->get_var('REQUEST_METHOD','server',PLIB_Input::STRING) == 'HEAD')
			return true;

		return $current_ua === $old_ua;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>