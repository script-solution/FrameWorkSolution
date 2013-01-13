<?php
/**
 * Contains the tree-manager
 * 
 * @package			FrameWorkSolution
 * @subpackage	tree
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
 * The tree-manager. This class can be used for any structure that represents
 * a tree. It gets the tree-nodes from the storage-implementation, organizes and
 * stores the structure in an efficient way so that it can be easily accessed any
 * manipulated.
 *
 * @package			FrameWorkSolution
 * @subpackage	tree
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Tree_Manager extends FWS_Object
{
	/**
	 * The storage-object
	 *
	 * @var FWS_Tree_Storage
	 */
	private $_storage;
	
	/**
	 * The root-node.
	 *
	 * @var FWS_Tree_Node
	 */
	private $_root;
	
	/**
	 * All nodes saved as:
	 * <code>
	 * 	array[<id>] = <node>
	 * </code>
	 *
	 * @var array
	 */
	private $_nodes = array();
	
	/**
	 * Constructor
	 *
	 * @param FWS_Tree_Storage $storage the storage-object (null = empty storage)
	 */
	public function __construct($storage = null)
	{
		parent::__construct();
		
		if($storage !== null && !($storage instanceof FWS_Tree_Storage))
			FWS_Helper::def_error('instance','storage','FWS_Tree_Storage',$storage);
		
		if($storage === null)
			$this->_storage = new FWS_Tree_Storage_Empty();
		else
			$this->_storage = $storage;
		
		// at first we have to store the data in a better way
		// so that we can create the tree-structure
		$roots = array();
		$childs = array();
		$nodes = array();
		foreach($this->_storage->get_nodes() as $data)
		{
			$id = $data->get_id();
			$parent_id = $data->get_parent_id();
			
			// store by id
			$nodes[$id] = $data;
			
			if($parent_id != 0)
			{
				if(!isset($childs[$parent_id]))
					$childs[$parent_id] = array();
				$childs[$parent_id][] = $id;
			}
			else
				$roots[] = $id;
		}
		
		// create root
		$root = new FWS_Tree_NodeData(0,'root');
		$this->_root = new FWS_Tree_Node($root);
		
		// create all root-nodes and append it to our root
		foreach($roots as $id)
		{
			$n = new FWS_Tree_Node($nodes[$id],$this->_root,$this->_root->get_layer() + 1);
			$this->_root->add_node($n);
			$this->_nodes[$id] = $n;
		}
		
		// append childs to the nodes
		foreach($childs as $pid => $cids)
		{
			$this->_create_parents($nodes,$pid);
			$p = $this->_nodes[$pid];
			
			foreach($cids as $cid)
			{
				$n = $p->add_data($nodes[$cid]);
				if($n !== null)
					$this->_nodes[$cid] = $n;
			}
		}
	}
	
	/**
	 * Removes all nodes from the tree
	 */
	public final function clear()
	{
		foreach($this->_nodes as $node)
			$this->remove_node($node->get_id());
	}
	
	/**
	 * Checks wether a node with given id exists
	 *
	 * @param int $id the id of the node
	 * @return boolean true if the node exists
	 */
	public final function node_exists($id)
	{
		if(!FWS_Helper::is_integer($id) || $id <= 0)
			FWS_Helper::def_error('intgt0','id',$id);

		return isset($this->_nodes[$id]);
	}

	/**
	 * @param int $id the id of the node
	 * @return string|boolean the name of the node with given id or false if not found
	 */
	public final function get_node_name($id)
	{
		if($this->node_exists($id))
			return $this->_nodes[$id]->get_name();

		return false;
	}

	/**
	 * @return int the total number of nodes
	 */
	public final function get_node_count()
	{
		return count($this->_nodes);
	}
	
	/**
	 * Stores all changes that have been performed.
	 * This method <b>has to</b> be called if changes to the nodes should be stored!
	 * Note that added or removed nodes will be stored directly and don't require the
	 * call of this method.
	 */
	public final function store_changes()
	{
		$nodes = array();
		foreach($this->_nodes as $node)
		{
			if($node->get_data()->has_changed())
				$nodes[] = $node->get_data();
		}
		
		if(count($nodes) > 0)
			$this->_storage->update_nodes($nodes);
	}
	
	/**
	 * Moves the node with given id to the given parent-id at given index
	 *
	 * @param int $id the id of the node
	 * @param int $parent_id the target-parent-id (0 = root)
	 * @param int $index the index at which the node should be added at the target-node.
	 * 	the default value is -1 which will insert the node at the end.
	 * @see store_changes()
	 */
	public final function move_node($id,$parent_id,$index = -1)
	{
		if(!FWS_Helper::is_integer($id) || $id <= 0)
			FWS_Helper::def_error('intgt0','id',$id);
		if(!FWS_Helper::is_integer($parent_id) || $parent_id < 0)
			FWS_Helper::def_error('intge0','parent_id',$parent_id);
		if(!FWS_Helper::is_integer($index) || $index < -1)
			FWS_Helper::def_error('numgex','index',-1,$index);
		if($parent_id == $id)
			FWS_Helper::error('You can\'t add the node as a child of itself ;)');
		
		// load nodes and check if they exist
		$n = $this->get_node($id);
		$newp = $parent_id == 0 ? $this->_root : $this->get_node($parent_id);
		if($n === null || $newp === null)
			return;
		
		if($n->has_child_node($parent_id,true))
			FWS_Helper::error('You can\'t add the node to a child-node of it ;)');
		
		// remove from the current node
		$p = $n->get_parent();
		if($p !== null)
			$p->remove_child($id);
		
		// insert at the new node
		if($index == -1)
			$index = $newp->get_child_count();
		
		$newp->add_node_at($n,$index);
	}
	
	/**
	 * Adds a node with the given data to the node with id <var>$parent_id</var>.
	 *
	 * @param int $parent_id the id of the parent-node (0 = root)
	 * @param FWS_Tree_NodeData $data the data
	 */
	public final function add_node($parent_id,$data)
	{
		$this->add_node_at($parent_id,$data,$this->get_child_count($parent_id));
	}
	
	/**
	 * Adds a node with the given data to the node with id <var>$parent_id</var> at
	 * the given index.
	 *
	 * @param int $parent_id the id of the parent-node (0 = root)
	 * @param FWS_Tree_NodeData $data the data
	 * @param int $index the index at which you want to add it (0..n)
	 */
	public final function add_node_at($parent_id,$data,$index)
	{
		if(!FWS_Helper::is_integer($parent_id) || $parent_id < 0)
			FWS_Helper::def_error('intge0','parent_id',$parent_id);
		if(!($data instanceof FWS_Tree_NodeData))
			FWS_Helper::def_error('instance','data','FWS_Tree_NodeData',$data);
		
		// is it a root-node?
		if($parent_id == 0)
			$n = $this->_root->add_data_at($data,$index);
		else
		{
			// get parent
			$parent = $this->get_node($parent_id);
			if($parent === null)
				FWS_Helper::error('A node with id '.$parent_id.' does not exist!');
			
			$n = $parent->add_data_at($data,$index);
		}
		
		// add the node to the node-list
		$this->_nodes[$n->get_id()] = $n;
		$this->_storage->add_node($data);
	}
	
	/**
	 * Removes the node with given id
	 *
	 * @param int $id the id of the node
	 */
	public final function remove_node($id)
	{
		if(!FWS_Helper::is_integer($id) || $id <= 0)
			FWS_Helper::def_error('intgt0','id',$id);
		
		$n = $this->get_node($id);
		if($n !== null)
		{
			$n->get_parent()->remove_child($id);
			
			$ids = array();
			$this->_collect_remove_ids($ids,$n);
			foreach($ids as $delid)
				unset($this->_nodes[$delid]);
			
			$this->_storage->remove_nodes($ids);
		}
	}

	/**
	 * Calculates the path to the node with given id.
	 * The path will be in the opposite order, that means the "deepest" node will be
	 * the first element and the root-node the last one.
	 *
	 * @param int $id the id of the node
	 * @return array an array of the form:
	 * 	<code>
	 * 		array(
	 * 			array(<name>,<id>),
	 * 			...
	 *		)
	 * 	</code>
	 */
	public final function get_path($id)
	{
		if(!FWS_Helper::is_integer($id) || $id <= 0)
			FWS_Helper::def_error('intgt0','id',$id);
		
		$node = $this->get_node($id);
		if($node !== null)
		{
			$path = array(array($node->get_name(),$id));
			$parent = $node->get_parent();
			while($parent !== null && !$parent->is_root())
			{
				$node = $this->get_node($node->get_data()->get_parent_id());
				$parent = $node->get_parent();
				$path[] = array($node->get_data()->get_name(),$node->get_id());
			}
			return $path;
		}

		return array();
	}

	/**
	 * Determines the id of the parent-node of the node with given id
	 * 
	 * @param int $id the id of the node
	 * @return int|bool the id of the parent node or false if not found
	 */
	public final function get_parent_id($id)
	{
		if(!FWS_Helper::is_integer($id) || $id <= 0)
			FWS_Helper::def_error('intgt0','id',$id);
		
		$node = $this->get_node($id);
		if($node !== null)
			return $node->get_data()->get_parent_id();

		return false;
	}

	/**
	 * Determines wether the given node has children
	 * 
	 * @param int $id the id of the node
	 * @return boolean true if the node with given id contains sub-nodes
	 */
	public final function has_childs($id)
	{
		if(!FWS_Helper::is_integer($id) || $id <= 0)
			FWS_Helper::def_error('intgt0','id',$id);
		
		return $this->get_child_count($id) > 0;
	}

	/**
	 * Determines the number of children for the given node-id
	 * 
	 * @param int $id the id of the node
	 * @return int the number of child-nodes
	 */
	public final function get_child_count($id)
	{
		if(!FWS_Helper::is_integer($id) || $id < 0)
			FWS_Helper::def_error('intge0','id',$id);
		
		if($id == 0)
			return $this->_root->get_child_count();
		
		$node = $this->get_node($id);
		if($node !== null)
			return $node->get_child_count();

		return 0;
	}

	/**
	 * Returns the node with given id
	 * 
	 * @param int $id the id of the node
	 * @return FWS_Tree_Node the node-object or null if not found
	 */
	public final function get_node($id)
	{
		if(!FWS_Helper::is_integer($id) || $id <= 0)
			FWS_Helper::def_error('intgt0','id',$id);
		
		if(isset($this->_nodes[$id]))
			return $this->_nodes[$id];

		return null;
	}
	
	/**
	 * Returns the data of the node with given id
	 *
	 * @param int $id the id of the node
	 * @return FWS_Tree_NodeData the node-data or null if not found
	 */
	public final function get_node_data($id)
	{
		$node = $this->get_node($id);
		if($node !== null)
			return $node->get_data();
		
		return null;
	}

	/**
	 * Collects all nodes with given ids
	 *
	 * @param array $ids a numeric array with the ids
	 * @return array an numeric array with {@link FWS_Tree_Node} objects
	 */
	public final function get_nodes_with_ids($ids)
	{
		if(!FWS_Array_Utils::is_integer($ids))
			FWS_Helper::def_error('intarray','ids',$ids);
		
		$result = array();
		foreach($ids as $id)
		{
			if(isset($this->_nodes[$id]))
				$result[] = $this->_nodes[$id];
		}

		return $result;
	}

	/**
	 * @return array an associative array with all node-objects ({@link FWS_Tree_Node}):
	 * 	<code>array(<id> => <node>,...)</code>
	 */
	public final function get_all_nodes()
	{
		$result = array();
		$this->_get_all_nodes($result,$this->_root);
		return $result;
	}
	
	/**
	 * Collects the direct sub-nodes of the node with given id
	 *
	 * @param int $id the id of the node of which you want to get the sub-nodes
	 * @return array an numeric array with all sub-nodes
	 */
	public final function get_direct_sub_nodes($id)
	{
		if(!FWS_Helper::is_integer($id) || $id < 0)
			FWS_Helper::def_error('intge0','id',$id);
		
		if($id == 0)
			$node = $this->_root;
		else
			$node = $this->get_node($id);
		
		if($node === null)
			return array();
		
		$res = array();
		foreach($node->get_childs() as $child)
			$res[] = $child;
		return $res;
	}

	/**
	 * Collects all sub-nodes of the given node recursivly.
	 *
	 * @param int $id the id of the node of which you want to get the sub-nodes
	 * @return array an numeric array with all sub-nodes
	 */
	public final function get_sub_nodes($id)
	{
		if(!FWS_Helper::is_integer($id) || $id < 0)
			FWS_Helper::def_error('intge0','id',$id);
		
		if($id == 0)
			return $this->get_all_nodes();
		
		$result = array();
		$node = $this->get_node($id);
		if($node !== null)
			$this->_get_all_nodes($result,$node);
		return $result;
	}
	
	/**
	 * Collects all sub-node-ids of the node with given id
	 *
	 * @param int $id the id of the node
	 * @return array an numeric array with all sub-node-ids
	 */
	public final function get_sub_node_ids($id)
	{
		$nodes = $this->get_sub_nodes($id);
		$ids = array();
		foreach($nodes as $node)
			$ids[] = $node->get_id();
		return $ids;
	}
	
	/**
	 * Collects all node-ids to remove
	 *
	 * @param array $ids a reference to the array with the ids
	 * @param FWS_Tree_Node $node the current node
	 */
	private function _collect_remove_ids(&$ids,$node)
	{
		$ids[] = $node->get_id();
		foreach($node->get_childs() as $child)
			$this->_collect_remove_ids($ids,$child);
	}

	/**
	 * Collects the nodes of the given node recursivly
	 *
	 * @param array $result [reference] the result-array
	 * @param FWS_Tree_Node $node the node
	 */
	private function _get_all_nodes(&$result,$node)
	{
		foreach($node->get_childs() as $child)
		{
			$result[] = $child;
			$this->_get_all_nodes($result,$child);
		}
	}
	
	/**
	 * Creates the required parent-nodes, starting with the given id (as parent)
	 *
	 * @param array $nodes the node-data-array
	 * @param int $pid the id of the first parent that should exist
	 */
	private function _create_parents($nodes,$pid)
	{
		if($pid > 0 && !isset($this->_nodes[$pid]))
		{
			$this->_create_parents($nodes,$nodes[$pid]->get_parent_id());
			$ppid = $nodes[$pid]->get_parent_id();
			// $ppid is always > 0, because we've already created all root-nodes
			$n = $this->_nodes[$ppid]->add_data($nodes[$pid]);
			if($n !== null)
				$this->_nodes[$pid] = $n;
		}
	}
	
	/**
	 * Creates an XML-tree for the current tree-structure.
	 *
	 * @return string the XML-document
	 */
	public final function to_xml()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xml .= '<nodes>'."\n";
		foreach($this->_root->get_childs() as $node)
			$xml .= $node->to_xml(1);
		$xml .= '</nodes>';
		return $xml;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>