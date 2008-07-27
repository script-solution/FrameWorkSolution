<?php
/**
 * Contains the php-session-storage-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	session.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The session-storage implementation for the PHP-session
 *
 * @package			PHPLib
 * @subpackage	session.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Session_Storage_PHP extends PLIB_Object implements PLIB_Session_Storage
{
	/**
	 * The index of the session-data
	 */
	const SESS_INDEX = 'plib_sess';
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		// ensure that the session is started
		session_name('sid');
		session_start();
	}
	
	/**
	 * @see PLIB_Session_Storage::load_list()
	 *
	 * @return array
	 */
	public function load_list()
	{
		if(!isset($_SESSION[self::SESS_INDEX]))
			return array();
		
		// we now just the current user
		return array(new PLIB_Session_Data(
			$_SESSION[self::SESS_INDEX]['sid'],
			$_SESSION[self::SESS_INDEX]['uid'],
			$_SESSION[self::SESS_INDEX]['uip'],
			$_SESSION[self::SESS_INDEX]['uname'],
			$_SESSION[self::SESS_INDEX]['date'],
			$_SESSION[self::SESS_INDEX]['uagent'],
			$_SESSION[self::SESS_INDEX]['data']
		));
	}

	/**
	 * @see PLIB_Session_Storage::add_user()
	 *
	 * @param PLIB_Session_Data $user
	 */
	public function add_user($user)
	{
		$this->update_user($user);
	}

	/**
	 * @see PLIB_Session_Storage::get_new_user()
	 *
	 * @return PLIB_Session_Data
	 */
	public function get_new_user()
	{
		return new PLIB_Session_Data();
	}

	/**
	 * @see PLIB_Session_Storage::remove_user()
	 *
	 * @param array $ids
	 */
	public function remove_user($ids)
	{
		if(isset($_SESSION[self::SESS_INDEX]['sid']) && in_array($_SESSION[self::SESS_INDEX]['sid'],$ids))
		{
			$_SESSION[self::SESS_INDEX] = array();
			session_destroy();
		}
	}

	/**
	 * @see PLIB_Session_Storage::update_user()
	 *
	 * @param PLIB_Session_Data $user
	 */
	public function update_user($user)
	{
		$_SESSION[self::SESS_INDEX] = array(
			'sid' => $user->get_session_id(),
			'uid' => $user->get_user_id(),
			'uip' => $user->get_user_ip(),
			'uname' => $user->get_user_name(),
			'date' => $user->get_date(),
			'uagent' => $user->get_user_agent(),
			'data' => $user->get_session_data()
		);
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>