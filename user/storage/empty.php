<?php
/**
 * Contains the empty-user-storage-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	user.storage
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
 * An empty implementation for the user-storage. This can be used if you have no user-management
 * 
 * @package			FrameWorkSolution
 * @subpackage	user.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_User_Storage_Empty extends FWS_Object implements FWS_User_Storage
{
	/**
	 * @see FWS_User_Storage::check_user()
	 *
	 * @param unknown_type $userdata
	 * @return int
	 */
	public function check_user($userdata)
	{
		return FWS_User_Current::LOGIN_ERROR_NO_ERROR;
	}

	/**
	 * @see FWS_User_Storage::verify_password()
	 *
	 * @param string &$pw
	 * @param FWS_User_Data $data
	 * @return int
	 */
	public function check_password(&$pw,$data)
	{
		// dummy implementation
		return FWS_User_Current::LOGIN_ERROR_PW_INCORRECT;
	}

	/**
	 * @see FWS_User_Storage::get_userdata_by_id()
	 *
	 * @param int $id
	 * @return FWS_User_Data
	 */
	public function get_userdata_by_id($id)
	{
		// dummy implementation
		return new FWS_User_Data(0,'','');
	}

	/**
	 * @see FWS_User_Storage::get_userdata_by_name()
	 *
	 * @param string $name
	 * @return FWS_User_Data
	 */
	public function get_userdata_by_name($name)
	{
		// dummy implementation
		return new FWS_User_Data(0,'','');
	}

	/**
	 * @see FWS_User_Storage::login()
	 *
	 * @param int $id
	 */
	public function login($id)
	{
		// do nothing
	}

	/**
	 * @see FWS_User_Storage::logout()
	 *
	 * @param int $id
	 */
	public function logout($id)
	{
		// do nothing
	}

	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>