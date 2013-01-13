<?php
/**
 * Contains the session-storage-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	session
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
 * The interface for all session-store-types
 * 
 * @package			FrameWorkSolution
 * @subpackage	session
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Session_Storage
{
	/**
	 * Loads the list and returns an array {@link FWS_Session_Data} objects
	 * 
	 * @return array all online user
	 */
	public function load_list();
	
	/**
	 * Should build and return a new {@link FWS_Session_Data} object.
	 * 
	 * @return FWS_Session_Data the user-object
	 */
	public function get_new_user();
	
	/**
	 * Adds the given entry
	 * 
	 * @param FWS_Session_Data $user the {@link FWS_Session_Data} object
	 */
	public function add_user($user);
	
	/**
	 * Updates the given entry
	 * 
	 * @param FWS_Session_Data $user the {@link FWS_Session_Data} object
	 */
	public function update_user($user);
	
	/**
	 * Removes the entries with given session-ids from the online-list
	 * 
	 * @param array $ids all session-ids to delete
	 */
	public function remove_user($ids);
}
?>