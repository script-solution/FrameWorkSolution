<?php
/**
 * Contains the tree-node-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	tree
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The node in the tree. Contains layer, the parent-node, an array of childs and
 * data which is associated with this node.
 * <br>
 * Note that this class does not check many parameters for performance issues!
 *
 * @package			PHPLib
 * @subpackage	tree
 * @author			Nils Asmussen <nils@script-solution.de>
 */
final class PLIB_Tree_Node extends PLIB_FullObject
{
	/**
	 * The id of the node (copied from the data)
	 *
	 * @var int
	 */
	private $_id;
	
	/**
	 * The data of this node
	 *
	 * @var PLIB_Tree_NodeData
	 */
	private $_data;
	
	/**
	 * The layer of this node. This will be set by the tree-manager!
	 * The first layer is 0! -1 is the layer of the root-node.
	 *
	 * @var int
	 */
	private $_layer;
	
	/**
	 * The parent-node (not the id!). This will be set by the tree-manager!
	 *
	 * @var PLIB_Tree_Node
	 */
	private $_parent;
	
	/**
	 * An array with all child-nodes. This will be set by the tree-manager!
	 *
	 * @var array
	 */
	private $_childs = array();
	
	/**
	 * Cache the ids for faster access
	 *
	 * @var array
	 */
	private $_child_ids = array();
	
	/**
	 * Constructor
	 *
	 * @param PLIB_Tree_NodeData $data the data of this node
	 * @param PLIB_Tree_Node $parent the parent-node
	 * @param int $layer the layer of this node
	 */
	public function __construct($data,$parent = null,$layer = -1)
	{
		$this->_id = $data->get_id();
		$this->_parent = $parent;
		$this->_layer = $layer;
		$this->_data = $data;
	}
	
	/**
	 * Note that this indicates wether this is the "real" root-node not wether it is
	 * one root-node from the perspective of the user!
	 * 
	 * @return boolean wether this node is a root-node
	 */
	public function is_root()
	{
		return $this->_parent === null;
	}
	
	/**
	 * @return PLIB_Tree_Node the parent-node (null for the root-node)
	 */
	public function get_parent()
	{
		return $this->_parent;
	}
	
	/**
	 * @return int the id of the node
	 */
	public function get_id()
	{
		return $this->_id;
	}

	/**
	 * @return string the name of the node
	 */
	public function get_name()
	{
		return $this->_data->get_name();
	}
	
	/**
	 * @return int the layer of this node (starting with 0)
	 */
	public function get_layer()
	{
		return $this->_layer;
	}
	
	/**
	 * @return PLIB_Tree_NodeData the data of this node
	 */
	public function get_data()
	{
		return $this->_data;
	}
	
	/**
	 * Sets the data of this node
	 *
	 * @param PLIB_Tree_NodeData $data the new value
	 */
	public function set_data($data)
	{
		if(!($data instanceof PLIB_Tree_NodeData))
			PLIB_Helper::def_error('instance','data','PLIB_Tree_NodeData',$data);
		
		$this->_data = $data;
	}
	
	/**
	 * Checks wether this node has a child-node with given id. If required
	 * the method checks this recursivly.
	 *
	 * @param int $id the id of the node you're looking for
	 * @param boolean $recursive search recursivly?
	 */
	public function has_child_node($id,$recursive = false)
	{
		foreach($this->_childs as $child)
		{
			if($child->_id == $id)
				return true;
			
			if($recursive && $child->has_child_node($id,$recursive))
				return true;
		}
		
		return false;
	}

	/**
	 * Takes the given data, creates a node with the data and adds it add at the position
	 * corresponding to its sort-value.
	 * Note that the parent-id will be set in the data!
	 * <b>This method may just be called by the tree-manager!</b>
	 *
	 * @param PLIB_Tree_NodeData $data the data to add
	 * @return PLIB_Tree_Node the created node or null if nothing has been done
	 */
	public function add_data($data)
	{
		return $this->add_data_at($data,$this->_get_position($data->get_sort()));
	}
	
	/**
	 * Takes the given data, creates a node with the data and adds it to the given
	 * index into the child-array.
	 * Note that the parent-id will be set in the data!
	 * <b>This method may just be called by the tree-manager!</b>
	 * 
	 * @param PLIB_Tree_NodeData $data the data to add
	 * @param int $index the index where the node should be added (0..n)
	 * @return PLIB_Tree_Node the created node or null if nothing has been done
	 */
	public function add_data_at($data,$index)
	{
		$node = new PLIB_Tree_Node($data,$this,$this->_layer + 1);
		return $this->add_node_at($node,$index);
	}
	
	/**
	 * Adds the given node at the position corresponding to its sort-value
	 * Note that the parent-id will be set in the data!
	 * <b>This method may just be called by the tree-manager!</b>
	 *
	 * @param PLIB_Tree_Node $node the node to add
	 * @return PLIB_Tree_Node the created node or null if nothing has been done
	 */
	public function add_node($node)
	{
		return $this->add_node_at($node,$this->_get_position($node->get_data()->get_sort()));
	}
	
	/**
	 * Adds the given node to the given index into the child-array.
	 * Note that the parent-id will be set in the data!
	 * <b>This method may just be called by the tree-manager!</b>
	 * 
	 * @param PLIB_Tree_Node $node the node to add
	 * @param int $index the index where the node should be added (0..n)
	 * @return PLIB_Tree_Node the created node or null if nothing has been done
	 */
	public function add_node_at($node,$index)
	{
		// check wether this data does already exist
		$nid = $node->get_id();
		if(isset($this->_child_ids[$nid]))
			return null;
		
		$data = $node->_data;
		
		$data->set_parent_id($this->_id);
		
		// add at given index
		$this->_child_ids[$node->get_id()] = true;
		array_splice($this->_childs,$index,0,array($node));
		
		return $node;
	}
	
	/**
	 * @return int the number of childs
	 */
	public function get_child_count()
	{
		return count($this->_childs);
	}
	
	/**
	 * @return array a numeric array with all child-nodes
	 */
	public function get_childs()
	{
		return $this->_childs;
	}
	
	/**
	 * Removes the child with given id from this node.
	 * <b>This method may just be called by the tree-manager!</b>
	 *
	 * @param int $id the id of the child
	 */
	public function remove_child($id)
	{
		foreach($this->_childs as $i => $child)
		{
			if($child->_id == $id)
			{
				// move the following childs one step back
				for($x = $i,$len = count($this->_childs);$x < $len - 1;$x++)
				{
					$this->_childs[$x] = $this->_childs[$x + 1];
					$this->_childs[$x]->_data->set_sort($x + 1);
				}
				// delete the last one
				unset($this->_childs[count($this->_childs) - 1]);
				break;
			}
		}
	}
	
	/**
	 * @return array an associative array with all fields to use for the XML-representation
	 */
	public function to_xml($layer)
	{
		$indent = '';
		for($i = 0;$i < $layer;$i++)
			$indent .= "\t";
		
		$str = $indent.'<node>'."\n";
		foreach($this->_data->get_xml_properties() as $k => $v)
			$str .= $indent."\t".'<'.$k.'>'.$v.'</'.$k.'>'."\n";
		
		$str .= $indent."\t".'<childs>'."\n";
		foreach($this->_childs as $child)
			$str .= $child->to_xml($layer + 2);
		$str .= $indent."\t".'</childs>'."\n";
		
		$str .= $indent.'</node>'."\n";
		return $str;
	}
	
	/**
	 * Determines the position for the given sort-value
	 *
	 * @param int $sort the sort-value of the node
	 * @return int the position
	 */
	private function _get_position($sort)
	{
		$pos = 0;
		foreach($this->_childs as $child)
		{
			if($child->_data->get_sort() > $sort)
				break;
			$pos++;
		}
		return $pos;
	}
	
	protected function _get_print_vars()
	{
		// replace the parent to prevent recursion
		$vars = get_object_vars($this);
		$vars['_parent'] = $this->_parent === null ? null : '<'.$this->_parent->get_name().'>';
		return $vars;
	}
}
?>