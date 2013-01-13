<?php
/**
 * Contains the php-session-storage-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	session.storage
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
 * The session-storage implementation for the PHP-session
 *
 * @package			FrameWorkSolution
 * @subpackage	session.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Session_Storage_PHP extends FWS_Object implements FWS_Session_Storage
{
	/**
	 * The index of the session-data
	 */
	const SESS_INDEX = 'fws_sess';
	
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
	 * @see FWS_Session_Storage::load_list()
	 *
	 * @return array
	 */
	public function load_list()
	{
		if(!isset($_SESSION[self::SESS_INDEX]))
			return array();
		
		// we know just the current user
		return array(new FWS_Session_Data(
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
	 * @see FWS_Session_Storage::add_user()
	 *
	 * @param FWS_Session_Data $user
	 */
	public function add_user($user)
	{
		$this->update_user($user);
	}

	/**
	 * @see FWS_Session_Storage::get_new_user()
	 *
	 * @return FWS_Session_Data
	 */
	public function get_new_user()
	{
		return new FWS_Session_Data();
	}

	/**
	 * @see FWS_Session_Storage::remove_user()
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
	 * @see FWS_Session_Storage::update_user()
	 *
	 * @param FWS_Session_Data $user
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
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>