<?php
/**
 * Contains the tree-storage-interface
 *
 * @version			$Id: storage.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	tree
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all storage-implementation for the tree.
 *
 * @package			PHPLib
 * @subpackage	tree
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Tree_Storage
{
	/**
	 * Should read all nodes from the corresponding source and return an array
	 * of {@link PLIB_Tree_NodeData} objects.
	 * <b>The nodes have to be sorted by "parent_id ASC, sort ASC"!</b> That means
	 * that the primary order should be the parent-id and the secondary order the sort.
	 *
	 * @return array the nodes
	 */
	public function get_nodes();
	
	/**
	 * This method should update all given nodes (the data of them).
	 *
	 * @param array $nodes an array of {@link PLIB_Tree_NodeData} objects
	 */
	public function update_nodes($nodes);
	
	/**
	 * This method should add the given node-data to the corresponding destination.
	 *
	 * @param PLIB_Tree_NodeData $data the data to store
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