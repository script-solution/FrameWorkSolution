<?php
/**
 * Contains the tree-storage-interface
 * 
 * @package			FrameWorkSolution
 * @subpackage	tree
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
 * The interface for all storage-implementation for the tree.
 *
 * @package			FrameWorkSolution
 * @subpackage	tree
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface FWS_Tree_Storage
{
	/**
	 * Should read all nodes from the corresponding source and return an array
	 * of {@link FWS_Tree_NodeData} objects.
	 * <b>The nodes have to be sorted by "parent_id ASC, sort ASC"!</b> That means
	 * that the primary order should be the parent-id and the secondary order the sort.
	 *
	 * @return array the nodes
	 */
	public function get_nodes();
	
	/**
	 * This method should update all given nodes (the data of them).
	 *
	 * @param array $nodes an array of {@link FWS_Tree_NodeData} objects
	 */
	public function update_nodes($nodes);
	
	/**
	 * This method should add the given node-data to the corresponding destination.
	 *
	 * @param FWS_Tree_NodeData $data the data to store
	 */
	public function add_node($data);
	
	/**
	 * This method should remove the nodes with given ids from the corresponding destination.
	 *
	 * @param array $ids a numeric array with the ids to delete
	 */
	public function remove_nodes($ids);
}
?>