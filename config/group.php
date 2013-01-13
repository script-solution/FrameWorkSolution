<?php
/**
 * Contains the config-group-class
 * 
 * @package			FrameWorkSolution
 * @subpackage	config
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
 * The data of a config-group
 *
 * @package			FrameWorkSolution
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Config_Group extends FWS_Object
{
	/**
	 * The id of the group
	 *
	 * @var int
	 */
	private $_id;
	
	/**
	 * The id of the parent-group (0 for root-groups)
	 *
	 * @var int
	 */
	private $_parent_id;
	
	/**
	 * The name of the group
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * The title of the group (may be empty)
	 *
	 * @var string
	 */
	private $_title;
	
	/**
	 * The sort-key
	 *
	 * @var int
	 */
	private $_sort;
	
	/**
	 * Constructor
	 * 
	 * @param int $id the id of the group
	 * @param int $parent_id the id of the parent-group (0 for root-groups)
	 * @param string $name the name of the group
	 * @param string $title the title of the group (may be empty)
	 * @param int $sort the sort-key
	 */
	public function __construct($id,$parent_id,$name,$title,$sort)
	{
		parent::__construct();
		
		$this->_id = $id;
		$this->_parent_id = $parent_id;
		$this->_name = $name;
		$this->_title = $title;
		$this->_sort = $sort;
	}

	/**
	 * @return int the id of the group
	 */
	public final function get_id()
	{
		return $this->_id;
	}

	/**
	 * @return int the id of the parent-group (0 for root-groups)
	 */
	public final function get_parent_id()
	{
		return $this->_parent_id;
	}

	/**
	 * @return string the name of the group
	 */
	public final function get_name()
	{
		return $this->_name;
	}

	/**
	 * @return string the title of the group (may be empty)
	 */
	public final function get_title()
	{
		return $this->_title;
	}

	/**
	 * @return int the sort-key
	 */
	public final function get_sort()
	{
		return $this->_sort;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>