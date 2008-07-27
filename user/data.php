<?php
/**
 * Contains the user-data-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	user
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * This class contains all data which belongs to the user.
 * You may extend this class to add more data.
 * 
 * @package			PHPLib
 * @subpackage	user
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_User_Data extends PLIB_Object
{
	/**
	 * The id of the user (0 if it is a guest)
	 *
	 * @var integer
	 */
	private $_user_id = 0;
	
	/**
	 * The name of the user (empty if it is a guest)
	 *
	 * @var string
	 */
	private $_user_name = '';
	
	/**
	 * The hashed password of the user
	 *
	 * @var string
	 */
	private $_user_pw = '';
	
	/**
	 * Constructor
	 * 
	 * @param int $id the user-id
	 * @param string $user_name the user-name
	 * @param string $user_pw the password
	 */
	public function __construct($id,$user_name,$user_pw)
	{
		parent::__construct();
		
		$this->_user_id = $id;
		$this->_user_name = $user_name;
		$this->_user_pw = $user_pw;
	}

	/**
	 * @return int the user-id
	 */
	public final function get_user_id()
	{
		return $this->_user_id;
	}
	
	/**
	 * @return string the user-name
	 */
	public final function get_user_name()
	{
		return $this->_user_name;
	}

	/**
	 * @return string the user-name
	 */
	public final function get_user_pw()
	{
		return $this->_user_pw;
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>