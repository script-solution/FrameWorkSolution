<?php
/**
 * Contains the class which represents the current user
 * 
 * @package			FrameWorkSolution
 * @subpackage	user
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
 * Represents the current user. Contains a {@link FWS_Session_Data} object and some
 * more information for the current user. It manages the login-state and some
 * other stuff.
 * <br>
 * Note that you have to call {@link init} if you want to allow logins!
 *
 * @package			FrameWorkSolution
 * @subpackage	user
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_User_Current extends FWS_Object
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
	 * The password is needs to be rehashed
	 */
	const LOGIN_ERROR_PW_OUTDATED							= 3;
	
	/**
	 * The data of the user
	 *
	 * @var FWS_Session_Data
	 */
 	protected $_user;
	
	/**
	 * The data of this user (null if it is a guest)
	 *
	 * @var FWS_User_Data
	 */
	protected $_userdata = null;
	
	/**
	 * The storage-object for the userdata
	 *
	 * @var FWS_User_Storage
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
	 * @var boolean
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
	 * @param FWS_User_Storage $storage the storage-object to use
	 * @see initialize()
	 */
	public function __construct($storage)
	{
		parent::__construct();
		
		if(!($storage instanceof FWS_User_Storage))
			FWS_Helper::def_error('instance','storage','FWS_User_Storage',$storage);
		
		$this->_storage = $storage;
		$this->_init_session();
	}
	
	/**
	 * Inits all stuff to use this class. You HAVE to call this method if you want to allow logins.
	 */
	public function init()
	{
		$sessions = FWS_Props::get()->sessions();
		$cookies = FWS_Props::get()->cookies();

		if($sessions->sessions_enabled())
		{
			// login by cookie?
			if($this->_use_cookies && !$this->is_loggedin())
			{
				$user = $cookies->get_cookie('user');
				$pw = $cookies->get_cookie('pw');
				if($user != null && $pw != null)
					$this->login($user,$pw,false);
			}
			
			// fill userdata if not already done
			if($this->is_loggedin() && $this->_userdata === null)
			{
				$this->set_userdata($this->get_user_id());
				
				// check the user
				// Note that we do this at the point because if login() has been called
				// $this->_userdata is already set and has been checked, of course.
				if($this->check_user() != self::LOGIN_ERROR_NO_ERROR)
				{
					$this->_assign_new_session();
					$this->setup_guest();
				}
			}
		}
	}

	/**
	 * Updates the session-data to the user-object
	 */
	public function finalize()
	{
		// store the serialized session-data to write it to the session-storage
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
			FWS_Helper::def_error('string','theme',$theme);
		
		$this->_theme = $theme;
	}

	/**
	 * Returns the path to the given item. This allows you to provide different
	 * themes. If the theme is empty it returns:
	 * <code>FWS_Path::client_app().'theme/'.$item</code>
	 * otherwise:
	 * <code>FWS_Path::client_app().'themes/'.$this->_theme.'/'.$item</code>
	 * You may change this by overwriting this method!
	 *
	 * @param string $item the path to the item starting at a theme-folder
	 * @return string the complete path to the item (starting at the root-folder)
	 */
	public function get_theme_item_path($item)
	{
		if(empty($this->_theme))
			return FWS_Path::client_app().'theme/'.$item;
		
		return FWS_Path::client_app().'themes/'.$this->_theme.'/'.$item;
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
			FWS_Helper::def_error('inarray','type',array('A.B.C.D','A.B.C','A.B','none'),$type);

		$this->_ip_validation_type = $type;
	}

	/**
	 * @return boolean whether the user-agent will be validated
	 */
	public final function validate_user_agent()
	{
		return $this->_user_agent_validation;
	}

	/**
	 * Sets whether the user-agent should be validated.
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
	 * @return boolean whether cookies are used to login
	 */
	public final function use_cookies()
	{
		return $this->_use_cookies;
	}

	/**
	 * Sets whether to use cookies to login
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
			FWS_Helper::def_error('notempty','name',$name);

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
	 * @return FWS_User_Data the user-data-object
	 */
	public final function get_userdata()
	{
		return $this->_userdata;
	}

	/**
	 * returns the session-data with the given name
	 * or $default if not found
	 *
	 * @param string $name the name of the data
	 * @param mixed $default the value to return if the session-data is not present
	 * @return mixed the data or $default if not found
	 */
	public final function get_session_data($name,$default = false)
	{
		if(empty($name))
			FWS_Helper::def_error('notempty','name',$name);

		if(isset($this->_session_data[$name]))
			return $this->_session_data[$name];

		return $default;
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
			FWS_Helper::def_error('notempty','name',$name);

		$this->_session_data[$name] = $value;
	}
	
	/**
	 * Clears all session-data
	 */
	public final function clear_session_data()
	{
		$this->_session_data = array();
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
	 * @param boolean $hashpw do you want to calculate the hash of the pw before comparing it?
	 * @return int the error-code; see FWS_Session::LOGIN_ERROR_*
	 */
	public function login($user,$pw,$hashpw = true)
	{
		$loggedin = $this->set_userdata(0,$user);

		if($hashpw)
			$loggedin = $this->_storage->check_password($pw,$this->_userdata);
		
		if($loggedin == self::LOGIN_ERROR_NO_ERROR)
		{
			// TODO this does not work since $user is not necessarily escaped
			// perform stripslashes here because addslashes() has been called on the value
			// and we want to compare it with as it is
			$user = stripslashes($user);
	
			if(empty($pw))
				$loggedin = self::LOGIN_ERROR_PW_INCORRECT;
			else
				$loggedin = $this->check_user($user,$hashpw ? false : $pw);
		}
		
		// setup user or guest
		if($loggedin == self::LOGIN_ERROR_NO_ERROR)
			$this->setup_user($user,$pw);
		else
			$this->setup_guest();

		return $loggedin;
	}

	/**
	 * logs the user out. will destroy the session, delete the cookies and redirect the user
	 */
	public function logout()
	{
		$sessions = FWS_Props::get()->sessions();
		$cookies = FWS_Props::get()->cookies();

		$sessions->logout_user($this->get_session_id(),$this->get_user_ip());
		
		if($this->_use_cookies)
		{
			$cookies->delete_cookie('user');
			$cookies->delete_cookie('pw');
		}
		
		$this->_userdata = null;
		$this->_session_data = array();
	}
	
	/**
	 * Sets the userdata for the given user-id or username
	 * 
	 * @param int $id the user-id
	 * @param string|bool $user the user-name (or false, if the id should be used)
	 * @return int the error-code or self::LOGIN_ERROR_NO_ERROR
	 */
	protected function set_userdata($id,$user = false)
	{
		if($user === false)
			$userdata = $this->_storage->get_userdata_by_id($id);
		else
			$userdata = $this->_storage->get_userdata_by_name((string)$user);
		
		if(!$userdata)
			return self::LOGIN_ERROR_USER_NOT_FOUND;
		
		$this->_userdata = $userdata;
		$this->_user->set_user_id($userdata->get_user_id());
		$this->_user->set_user_name($userdata->get_user_name());
		
		return self::LOGIN_ERROR_NO_ERROR;
	}

	/**
	 * setups the required stuff for a loggedin user
	 *
	 * @param string $user the username
	 * @param string $pw the password
	 */
	protected function setup_user($user,$pw)
	{
		$cookies = FWS_Props::get()->cookies();

		if($this->_use_cookies)
		{
			$cookies->set_cookie('user',$user);
			$cookies->set_cookie('pw',$pw);
		}
	}

	/**
	 * setups the required stuff for a guest
	 */
	protected function setup_guest()
	{
		$cookies = FWS_Props::get()->cookies();

		$this->_user->make_guest();
		$this->_userdata = null;
		
		// delete the cookie to ensure that the user won't try to get logged in again
		if($this->_use_cookies)
		{
			$cookies->delete_cookie('user');
			$cookies->delete_cookie('pw');
		}
	}
	
	/**
	 * checks the user. if the user is loggedin a value is invalid he/she will be logged out
	 *
	 * @param string|boolean $user the username
	 * @param string|boolean $pw the password
	 * @return int the error-code or self::LOGIN_ERROR_NO_ERROR
	 */
	protected function check_user($user = false,$pw = false)
	{
		$loggedin = self::LOGIN_ERROR_NO_ERROR;
		
		if($this->_userdata === null)
			$loggedin = self::LOGIN_ERROR_USER_NOT_FOUND;
		else if($user && $this->_userdata->get_user_name() !== $user)
			$loggedin = self::LOGIN_ERROR_USER_NAME_INCORRECT;
		else if($pw && $this->_userdata->get_user_pw() !== $pw)
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
		$cookies = FWS_Props::get()->cookies();
		$input = FWS_Props::get()->input();
		$sessions = FWS_Props::get()->sessions();

		$user_ip = $this->_determine_user_ip();
		$user_agent = $this->_determine_user_agent();

		// retrieve session-id
		$session_id = '';
		if($cookies->isset_cookie('sid'))
			$session_id = $cookies->get_cookie('sid');
		else if($input->isset_var($this->_url_sid_name,'get'))
			$session_id = $input->get_var($this->_url_sid_name,'get',FWS_Input::STRING);

		// try to load the user from the session-manager
		$user = null;
		if(!empty($session_id))
			$user = $sessions->get_user($session_id,$user_ip);
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
			$this->_user = $sessions->get_new_user();
		}
		
		// store current ip, user-agent and date
		$this->_user->set_user_agent($user_agent);
		$this->_user->set_user_ip($user_ip);
		$this->_user->set_date(time());

		// create a new session?
		if($user === null)
		{
			$this->_assign_new_session();
			$sessions->add_user($this->_user);
		}

		if($sessions->sessions_enabled())
		{
			// refresh cookie
			$cookies->set_cookie(
				'sid',$this->_user->get_session_id(),$sessions->get_online_timeout()
			);
		}
	}
	
	/**
	 * Assignes a new session to the current user
	 */
	private function _assign_new_session()
	{
		// generate new session id and store it via cookie
		$this->_user->set_session_id($this->_generate_session_id());
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
		$input = FWS_Props::get()->input();

		$val = $input->get_var('HTTP_USER_AGENT','server',FWS_Input::STRING);
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
		$input = FWS_Props::get()->input();

		if($input->isset_var('REMOTE_ADDR','server'))
			return $input->get_var('REMOTE_ADDR','server',FWS_Input::STRING);

		$ip = getenv('REMOTE_ADDR');
		$ip = htmlspecialchars($ip,ENT_QUOTES);
		$ip = addslashes($ip);
		return $ip;
	}

	/**
	 * Checks whether <var>$old_ip</var> is equal to <var>$current_ip</var>.
	 * Whether it is "equal" depends on the ip-validation-type.
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
				$current = FWS_String::substr($current_ip,0,FWS_String::strrpos($current_ip,'.'));
				$stored = FWS_String::substr($old_ip,0,FWS_String::strrpos($old_ip,'.'));
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
	 * Checks whether the given user-agents are equal.
	 *
	 * @param string $old_ua the old user-agent
	 * @param string $current_ua the current user-agent
	 * @return boolean true if it is equal
	 */
	private function _check_user_agent($old_ua,$current_ua)
	{
		$input = FWS_Props::get()->input();

		if(!$this->_user_agent_validation)
			return true;

		// The browser sends a HEAD-request to the root-folder if the user has requested a page
		// which contains the java-applet. In this request the user-agent is different which
		// causes a logout if the user-agent-check is enabled.
		if($input->get_var('REQUEST_METHOD','server',FWS_Input::STRING) == 'HEAD')
			return true;

		return $current_ua === $old_ua;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>