<?php
/**
 * Contains the config-storage interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	config
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
 * The interface for all storage-methods for the config-items
 *
 * @package			FrameWorkSolution
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Config_Storage
{
	/**
	 * Loads all groups returns them
	 * 
	 * @return array the array with FWS_Config_Group objects
	 */
	public function get_groups();
	
	/**
	 * Loads all items in the given group. Note that this is the root-group-id. So you may
	 * need to load items of the sub-groups.
	 * The result <b>has to</b> have the "correct" order because it will be displayed in that order.
	 *
	 * @param int $id the group-id
	 * @return array an array of FWS_Config_Data objects
	 */
	public function get_items_of_group($id);
	
	/**
	 * Loads all items which match the given keyword.
	 * The result <b>has to</b> have the "correct" order because it will be displayed in that order.
	 *
	 * @param string $keyword the keyword
	 * @return array an array of FWS_Config_Data objects
	 */
	public function get_items_with($keyword);
	
	/**
	 * Stores the given value to the given item
	 *
	 * @param int $id the id of the item
	 * @param mixed $value the new value
	 */
	public function store($id,$value);
	
	/**
	 * Restores the default-value for the given item
	 *
	 * @param int $id the id of the item
	 */
	public function restore_default($id);
}
?>