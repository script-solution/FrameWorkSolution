<?php
/**
 * Contains the interface for all user-storage-types
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	user
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The interface for all user-storage-types
 * 
 * @package			PHPLib
 * @subpackage	user
 * @author			Nils Asmussen <nils@script-solution.de>
 */
interface PLIB_User_Storage
{
	/**
	 * Retrieves the userdata by the given id and returns it. The userdata should be an
	 * instance of {@link PLIB_User_Data}. If the user could not been found the method should return null.
	 * 
	 * @param int $id the id of the user
	 * @return PLIB_User_Data the userdata
	 */
	public function get_userdata_by_id($id);
	
	/**
	 * Retrieves the userdata by the given username and returns it. The userdata should be an
	 * instance of {@link PLIB_User_Data}. If the user could not been found the method should return null.
	 * 
	 * @param string $name the username
	 * @return PLIB_User_Data the userdata
	 */
	public function get_userdata_by_name($name);
	
	/**
	 * Returns the hash of the given password. You will receive the password that has been entered
	 * and should return the hash you would store so that it can be compared with the it
	 * 
	 * @param string $pw the entered password
	 * @param PLIB_User_Data $data the user-data
	 * @return string the hash of the password
	 */
	public function get_hash_of_pw($pw,$data);
	
	/**
	 * This method gives you the opportunity to perform additional checks. For example if
	 * the user is activated.
	 * 
	 * @param PLIB_User_Data the data of the user
	 * @return int the error-code or {@link PLIB_User_Current::LOGIN_ERROR_NO_ERROR}
	 */
	public function check_user($userdata);
	
	/**
	 * Logins the user with given id. You may perform some actions if this happens.
	 * 
	 * @param int $id the id of the user
	 */
	public function login($id);
	
	/**
	 * Logouts the user with given id. You may perform some actions if this happens.
	 * 
	 * @param int $id the id of the user
	 */
	public function logout($id);
}
?>