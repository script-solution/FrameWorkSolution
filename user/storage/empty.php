<?php
/**
 * Contains the empty-user-storage-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	user.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * An empty implementation for the user-storage. This can be used if you have no user-management
 * 
 * @package			FrameWorkSolution
 * @subpackage	user.storage
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class FWS_User_Storage_Empty extends FWS_Object implements FWS_User_Storage
{
	/**
	 * @see FWS_User_Storage::check_user()
	 *
	 * @param unknown_type $userdata
	 * @return int
	 */
	public function check_user($userdata)
	{
		return FWS_User_Current::LOGIN_ERROR_NO_ERROR;
	}

	/**
	 * @see FWS_User_Storage::get_hash_of_pw()
	 *
	 * @param string $pw
	 * @param FWS_User_Data $data
	 * @return string
	 */
	public function get_hash_of_pw($pw,$data)
	{
		// dummy implementation
		return $pw;
	}

	/**
	 * @see FWS_User_Storage::get_userdata_by_id()
	 *
	 * @param int $id
	 * @return FWS_User_Data
	 */
	public function get_userdata_by_id($id)
	{
		// dummy implementation
		return new FWS_User_Data(0,'','');
	}

	/**
	 * @see FWS_User_Storage::get_userdata_by_name()
	 *
	 * @param string $name
	 * @return FWS_User_Data
	 */
	public function get_userdata_by_name($name)
	{
		// dummy implementation
		return new FWS_User_Data(0,'','');
	}

	/**
	 * @see FWS_User_Storage::login()
	 *
	 * @param int $id
	 */
	public function login($id)
	{
		// do nothing
	}

	/**
	 * @see FWS_User_Storage::logout()
	 *
	 * @param int $id
	 */
	public function logout($id)
	{
		// do nothing
	}

	/**
	 * @see FWS_Object::get_dump_vars()
	 *
	 * @return array
	 */
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>