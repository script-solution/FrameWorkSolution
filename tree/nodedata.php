<?php
/**
 * Contains the node-data class
 *
 * @version			$Id: nodedata.php 744 2008-05-24 15:11:18Z nasmussen $
 * @package			PHPLib
 * @subpackage	tree
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The data of a node. Contains all that should be stored to a database, file or other
 * locations. You may extend this class to add more attributes.
 * <br>
 * The class recognizes changes to the attributes and stores wether something has changed.
 * This makes it possible to update just the nodes that have really changed.
 *
 * @package			PHPLib
 * @subpackage	tree
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Tree_NodeData extends PLIB_FullObject
{
	/**
	 * Stores wether something has changed
	 *
	 * @var boolean
	 */
	protected $_changed = false;
	
	/**
	 * The id of the node
	 *
	 * @var int
	 */
	protected $_id = null;
	
	/**
	 * The id of the parent-node. If this is the root-node the parent-id has
	 * to be 0.
	 *
	 * @var int
	 */
	protected $_parent_id = null;
	
	/**
	 * The number that will be used for the order of the nodes.
	 *
	 * @var int
	 */
	protected $_sort = null;
	
	/**
	 * The name of this node
	 *
	 * @var string
	 */
	protected $_name = null;
	
	/**
	 * Constructor
	 *
	 * @param int $id the id of the node
	 * @param string $name the name of the node
	 * @param int $parent_id the id of the parent-node. 0 = root
	 * @param int $sort the sort-number
	 */
	public function __construct($id,$name,$parent_id = 0,$sort = 1)
	{
		parent::__construct();
		
		if(!PLIB_Helper::is_integer($id) || $id < 0)
			PLIB_Helper::def_error('intge0','id',$id);
		
		$this->_id = $id;
		$this->set_name($name);
		$this->set_parent_id($parent_id);
		$this->set_sort($sort);
	}
	
	/**
	 * @return boolean wether any attribute of this object has changed
	 */
	public final function has_changed()
	{
		return $this->_changed;
	}
	
	/**
	 * @return int the id of the node
	 */
	public final function get_id()
	{
		return $this->_id;
	}

	/**
	 * @return string the name of the node
	 */
	public final function get_name()
	{
		return $this->_name;
	}

	/**
	 * Sets the name of this node
	 * 
	 * @param string $name the new value
	 */
	public final function set_name($name)
	{
		if(empty($name))
			PLIB_Helper::def_error('notempty','name',$name);
		
		$changed = $this->_name !== null && $this->_name != $name;
		$this->_name = $name;
		$this->_changed |= $changed;
	}

	/**
	 * @return int the id of the parent-node. 0 = root
	 */
	public final function get_parent_id()
	{
		return $this->_parent_id;
	}

	/**
	 * Sets the id of the parent-node.
	 * 
	 * @param int $parent_id the new value
	 */
	public final function set_parent_id($parent_id)
	{
		if(!PLIB_Helper::is_integer($parent_id) || $parent_id < 0)
			PLIB_Helper::def_error('intge0','parent_id',$parent_id);
		
		$changed = $this->_parent_id !== null && $this->_parent_id != $parent_id;
		$this->_parent_id = $parent_id;
		$this->_changed |= $changed;
	}

	/**
	 * @return int the sort-number
	 */
	public final function get_sort()
	{
		return $this->_sort;
	}

	/**
	 * Sets the number used for sorting the nodes
	 * 
	 * @param int $sort the new value
	 */
	public final function set_sort($sort)
	{
		if(!PLIB_Helper::is_integer($sort) || $sort <= 0)
			PLIB_Helper::def_error('intgt0','sort',$sort);
		
		$changed = $this->_sort !== null && $this->_sort != $sort;
		$this->_sort = $sort;
		$this->_changed |= $changed;
	}
	
	/**
	 * @return array all properties that should be stored to XML
	 */
	public function get_xml_properties()
	{
		return array(
			'id' => $this->_id,
			'parent_id' => $this->_parent_id,
			'name' => $this->_name,
			'sort' => $this->_sort
		);
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>