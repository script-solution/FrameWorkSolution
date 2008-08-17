<?php
/**
 * Contains the empty session-storage-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	session.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
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