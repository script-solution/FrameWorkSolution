<?php
/**
 * Contains the empty session-storage-class
 *
 * @version			$Id: empty.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	session.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The "empty" implementation for the session-storage. This class may be used if you don't want
 * to allow logins
 * 
 * @package			PHPLib
 * @subpackage	session.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Session_Storage_Empty extends PLIB_FullObject implements PLIB_Session_Storage
{
	public function load_list()
	{
		return array();
	}
	
	public function get_new_user()
	{
		return new PLIB_Session_Data();
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
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>