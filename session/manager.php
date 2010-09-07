<?php
/**
 * Contains the session-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	session
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This class manages all currently online user. It uses a storage-object
 * to support different storage-locations for the data.
 * <br>
 * Note that you have to call {@link garbage_collection} by yourself!
 *
 * @package			FrameWorkSolution
 * @subpackage	session
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Session_Manager extends FWS_Object
{
	/**
	 * The storage-class
	 *
	 * @var FWS_User_Storage
	 */
	private $_storage;
	
	/**
	 * the online-table (bs_sessions) with the currently online user
	 *
	 * @var array
	 */
	private $_user_list;
	
	/**
	 * Stores all user that have been added
	 *
	 * @var array
	 */
	private $_added_user = array();

	/**
	 * The online-timeout (default 5 minutes)
	 *
	 * @var integer
	 */
	private $_online_timeout = 300;
	
	/**
	 * Is the session enabled?
	 *
	 * @var boolean
	 */
	private $_enable_session = true;
	
	/**
	 * Stores wether this object has already been finalized
	 *
	 * @var boolean
	 */
	private $_finalized = false;

	/**
	 * constructor
	 *
	 * @param FWS_Session_Storage $storage the storage-object to use
	 * @param boolean $enable_session do you want to enable the session-management?
	 */
	public function __construct($storage,$enable_session = true)
	{
		parent::__construct();
		
		if(!($storage instanceof FWS_Session_Storage))
			FWS_Helper::def_error('instance','storage','FWS_Session_Storage',$storage);
		
		$this->_enable_session = (boolean)$enable_session;
		$this->_storage = $storage;
		
		if($enable_session)
			$this->_load_user_list();
	}
	
	/**
	 * Will update all changed user
	 * 
	 * @param bool $force if enabled, it will be done even if we've done that before
	 */
	public final function finalize($force = false)
	{
		$user = FWS_Props::get()->user();
		
		// do nothing if we do not store sessions
		if(!$this->_enable_session)
			return;
		
		// don't finalize twice
		if(!$force && $this->_finalized)
			return;
		
		// at first we have to finalize the current user so that it can update
		// some values to the user-object, if necessary
		$user->finalize();
		
		// add the new user
		foreach($this->_added_user as $suser)
			$this->_storage->add_user($suser);
		
		// now update all changed user-objects
		foreach($this->_user_list as $suser)
		{
			if(!in_array($suser,$this->_added_user) && $suser->has_changed())
				$this->_storage->update_user($suser);
		}
		
		$this->_finalized = true;
	}

	/**
	 * Logouts the timedout user
	 *
	 * @see set_online_timeout()
	 */
	public function garbage_collection()
	{
		$user = FWS_Props::get()->user();
		
		$del = array();
		$current_sess = $user->get_session_id();
		foreach($this->_user_list as $id => $suser)
		{
			if($this->check_online_timeout($suser,$current_sess))
			{
				unset($this->_user_list[$id]);
				$del[] = $suser->get_session_id();
			}
		}

		if(count($del) > 0)
			$this->_storage->remove_user($del);
	}
	
	/**
	 * Checks wether the given user should be logged out because the timeout is over or not
	 *
	 * @param FWS_Session_Data $user the user to check
	 * @param string $currentsid the session-id of the current user
	 * @return bool true if its over
	 */
	protected function check_online_timeout($user,$currentsid)
	{
		return $user->get_date() < (time() - $this->_online_timeout) &&
				$user->get_session_id() != $currentsid;
	}
	
	/**
	 * @return boolean wether sessions are enabled
	 */
	public final function sessions_enabled()
	{
		return $this->_enable_session;
	}

	/**
	 * @return int the online-timeout
	 */
	public final function get_online_timeout()
	{
		return $this->_online_timeout;
	}

	/**
	 * Sets the online-timeout.
	 *
	 * @param int $timeout the new value
	 */
	public final function set_online_timeout($timeout)
	{
		if(!FWS_Helper::is_integer($timeout) || $timeout <= 0)
			FWS_Helper::def_error('intgt0','timeout',$timeout);

		$this->_online_timeout = $timeout;
	}
	
	/**
	 * @return FWS_Session_Data a new object of {@link FWS_Session_Data}
	 */
	public final function get_new_user()
	{
		return $this->_storage->get_new_user();
	}
	
	/**
	 * @return int the number of online user
	 */
	public final function get_online_count()
	{
		return count($this->_user_list);
	}
	
	/**
	 * @return array the list with the currently online user
	 * @see FWS_Session_Data
	 */
	public final function get_online_list()
	{
		return $this->_user_list;
	}
	
	/**
	 * Adds the given user to the list
	 * 
	 * @param FWS_Session_Data $user the user to add
	 */
	public final function add_user($user)
	{
		if(!($user instanceof FWS_Session_Data))
			FWS_Helper::def_error('instance','user','FWS_Session_Data',$user);
		
		$this->_user_list[$user->get_session_id().$user->get_user_ip()] = $user;
		$this->_added_user[] = $user;
	}
	
	/**
	 * Returns the {@link FWS_Session_Data} object for the given session-id and user-ip.
	 * Note that both values are required because we group all users by both values.
	 * 
	 * @param string $session_id the session-id of the user
	 * @param string $user_ip the ip user the user
	 * @return FWS_Session_Data the user-object or null if not found
	 */
	public final function get_user($session_id,$user_ip)
	{
		if(isset($this->_user_list[$session_id.$user_ip]))
			return $this->_user_list[$session_id.$user_ip];
		
		return null;
	}
	
	/**
	 * Logouts the user with given data
	 * 
	 * @param string $session_id the session-id of the user
	 * @param string $user_ip the ip user the user
	 */
	public final function logout_user($session_id,$user_ip)
	{
		$entry = $this->get_user($session_id,$user_ip);
		if($entry !== null)
		{
			$entry->make_guest();
			// update the storage immediatly
			$this->_storage->update_user($entry);
		}
	}

	/**
	 * Loads the user-list
	 */
	private function _load_user_list()
	{
		$list = $this->_storage->load_list();
		$this->_user_list = array();
		foreach($list as $user)
			$this->_user_list[$user->get_session_id().$user->get_user_ip()] = $user;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>