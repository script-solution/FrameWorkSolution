<?php
/**
 * Contains the session-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	session
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This class manages all currently online user. It uses a storage-object
 * to support different storage-locations for the data.
 *
 * @package			PHPLib
 * @subpackage	session
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Session_Manager extends PLIB_FullObject implements PLIB_Initable
{
	/**
	 * The storage-class
	 *
	 * @var PLIB_User_Storage
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
   * constructor
   *
	 * @param PLIB_Session_Storage $storage the storage-object to use
	 * @param boolean $enable_session do you want to enable the session-management?
   */
  public function __construct($storage,$enable_session = true)
  {
		parent::__construct();
		
		if(!($storage instanceof PLIB_Session_Storage))
			PLIB_Helper::def_error('instance','storage','PLIB_Session_Storage',$storage);
		
		$this->_enable_session = (boolean)$enable_session;
		$this->_storage = $storage;
		
		if($enable_session)
			$this->_load_user_list();
  }

	/**
	 * Logouts the timedout user
	 *
	 * @see set_online_timeout()
	 */
  public final function init()
  {
    $del = array();
    $current_sess = $this->user->get_session_id();
    foreach($this->_user_list as $id => $user)
    {
    	if($this->_check_online_timeout($user,$current_sess))
      {
        unset($this->_user_list[$id]);
        $del[] = $user->get_session_id();
      }
    }

    if(count($del) > 0)
    	$this->_storage->remove_user($del);
  }
  
  /**
   * Checks wether the given user should be logged out because the timeout is over or not
   *
   * @param PLIB_Session_Data $user the user to check
   * @param string $currentsid the session-id of the current user
   */
  protected function _check_online_timeout($user,$currentsid)
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
  	if(!PLIB_Helper::is_integer($timeout) || $timeout <= 0)
  		PLIB_Helper::def_error('intgt0','timeout',$timeout);

  	$this->_online_timeout = $timeout;
  }
  
  /**
   * @return PLIB_Session_Data a new object of {@link PLIB_Session_Data}
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
   * @see PLIB_Session_Data
   */
  public final function get_online_list()
  {
  	return $this->_user_list;
  }
  
  /**
   * Adds the given user to the list
   * 
   * @param PLIB_Session_Data $user the user to add
   */
  public final function add_user($user)
  {
  	if(!($user instanceof PLIB_Session_Data))
  		PLIB_Helper::def_error('instance','user','PLIB_Session_Data',$user);
  	
  	$this->_user_list[$user->get_session_id().$user->get_user_ip()] = $user;
  	$this->_added_user[] = $user;
  }
  
  /**
   * Returns the {@link PLIB_Session_Data} object for the given session-id and user-ip.
   * Note that both values are required because we group all users by both values.
   * 
   * @param string $session_id the session-id of the user
   * @param string $user_ip the ip user the user
   * @return PLIB_Session_Data the user-object or null if not found
   */
  public final function get_user($session_id,$user_ip)
  {
  	if(isset($this->_user_list[$session_id.$user_ip]))
  		return $this->_user_list[$session_id.$user_ip];
  	
  	return null;
  }
  
  /**
   * Will update all changed user
   */
  public final function finalize()
  {
  	// do nothing if we do not store sessions
  	if(!$this->_enable_session)
  		return;
  	
		// at first we have to finalize the current user so that it can update
		// some values to the user-object, if necessary
		$this->user->finalize();
		
		// add the new user
		foreach($this->_added_user as $user)
			$this->_storage->add_user($user);
		
		// now update all changed user-objects
  	foreach($this->_user_list as $user)
  	{
  		if(!in_array($user,$this->_added_user) && $user->has_changed())
				$this->_storage->update_user($user);
  	}
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
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>