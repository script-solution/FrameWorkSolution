<?php
/**
 * Contains the empty session-storage-class
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
 * The "empty" implementation for the session-storage. This class may be used if you don't want
 * to allow logins
 * 
 * @package			FrameWorkSolution
 * @subpackage	session.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Session_Storage_Empty extends FWS_Object implements FWS_Session_Storage
{
	public function load_list()
	{
		return array();
	}
	
	public function get_new_user()
	{
		return new FWS_Session_Data();
	}
	
	public function add_user($user)
	{
		// do nothing
	}
	
	public function update_user($user)
	{
		// do nothing
	}
	
	public function remove_user($ids)
	{
		// do nothing
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>