<?php
/**
 * Contains the config-group-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The data of a config-group
 *
 * @package			PHPLib
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Config_Group extends PLIB_FullObject
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
	
	protected function _get_print_vars()
	{
		return get_print_vars($this);
	}
}
?>