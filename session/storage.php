<?php
/**
 * Contains the session-storage-interface
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	session
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all session-store-types
 * 
 * @package			PHPLib
 * @subpackage	session
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_Session_Storage
{
	/**
	 * Loads the list and returns an array {@link PLIB_Session_Data} objects
	 * 
	 * @return array all online user
	 */
	public function load_list();
	
	/**
	 * Should build and return a new {@link PLIB_Session_Data} object.
	 * 
	 * @return PLIB_Session_Data the user-object
	 */
	public function get_new_user();
	
	/**
	 * Adds the given entry
	 * 
	 * @param PLIB_Session_Data $user the {@link PLIB_Session_Data} object
	 */
	public function add_user($user);
	
	/**
	 * Updates the given entry
	 * 
	 * @param PLIB_Session_Data $user the {@link PLIB_Session_Data} object
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