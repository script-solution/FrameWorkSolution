<?php
/**
 * Contains the user-data-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	user
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
 * This class contains all data which belongs to the user.
 * You may extend this class to add more data.
 * 
 * @package			FrameWorkSolution
 * @subpackage	user
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_User_Data extends FWS_Object
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
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>