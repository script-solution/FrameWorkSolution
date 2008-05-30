<?php
/**
 * Contains the config-storage interface
 *
 * @version			$Id: storage.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all storage-methods for the config-items
 *
 * @package			PHPLib
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Config_Storage
{
	/**
	 * Loads all groups returns them
	 * 
	 * @return array the array with PLIB_Config_Group objects
	 */
	public function get_groups();
	
	/**
	 * Loads all items in the given group. Note that this is the root-group-id. So you may
	 * need to load items of the sub-groups.
	 * The result <b>has to</b> have the "correct" order because it will be displayed in that order.
	 *
	 * @param int $id the group-id
	 * @return array an array of PLIB_Config_Data objects
	 */
	public function get_items_of_group($id);
	
	/**
	 * Loads all items which match the given keyword.
	 * The result <b>has to</b> have the "correct" order because it will be displayed in that order.
	 *
	 * @param string $keyword the keyword
	 * @return array an array of PLIB_Config_Data objects
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