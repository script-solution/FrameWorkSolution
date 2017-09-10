<?php
/**
 * Contains the interface for all user-storage-types
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
 * The interface for all user-storage-types
 * 
 * @package			FrameWorkSolution
 * @subpackage	user
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_User_Storage
{
	/**
	 * Retrieves the userdata by the given id and returns it. The userdata should be an
	 * instance of {@link FWS_User_Data}. If the user could not been found the method should return null.
	 * 
	 * @param int $id the id of the user
	 * @return FWS_User_Data the userdata
	 */
	public function get_userdata_by_id($id);
	
	/**
	 * Retrieves the userdata by the given username and returns it. The userdata should be an
	 * instance of {@link FWS_User_Data}. If the user could not been found the method should return null.
	 * 
	 * @param string $name the username
	 * @return FWS_User_Data the userdata
	 */
	public function get_userdata_by_name($name);
	
	/**
	 * Verifies the given password
	 * 
	 * @param string $pw the entered password; should be changed to the hash
	 * @param FWS_User_Data $data the user-data
	 * @return int the error code or {@link FWS_User_Current::LOGIN_ERROR_NO_ERROR}
	 */
	public function check_password(&$pw,$data);
	
	/**
	 * This method gives you the opportunity to perform additional checks. For example if
	 * the user is activated.
	 * 
	 * @param FWS_User_Data $userdata the data of the user
	 * @return int the error-code or {@link FWS_User_Current::LOGIN_ERROR_NO_ERROR}
	 */
	public function check_user($userdata);
	
	/**
	 * Logins the user with given id. You may perform some actions if this happens.
	 * 
	 * @param int $id the id of the user
	 */
	public function login($id);
	
	/**
	 * Logouts the user with given id. You may perform some actions if this happens.
	 * 
	 * @param int $id the id of the user
	 */
	public function logout($id);
}
?>