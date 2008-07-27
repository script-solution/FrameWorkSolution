<?php
/**
 * Contains the empty-tree-storage implementation
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tree.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * An empty implementation for the tree-storage
 *
 * @package			PHPLib
 * @subpackage	tree.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Tree_Storage_Empty extends PLIB_Object implements PLIB_Tree_Storage
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
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>