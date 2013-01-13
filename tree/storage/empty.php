<?php
/**
 * Contains the empty-tree-storage implementation
 * 
 * @package			FrameWorkSolution
 * @subpackage	tree.storage
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
 * An empty implementation for the tree-storage
 *
 * @package			FrameWorkSolution
 * @subpackage	tree.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_Tree_Storage_Empty extends FWS_Object implements FWS_Tree_Storage
{
	public function get_nodes()
	{
		return array();
	}
	
	public function update_nodes($nodes)
	{
		// do noting
	}
	
	public function add_node($data)
	{
		// do noting
	}
	
	public function remove_nodes($ids)
	{
		// do noting
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>