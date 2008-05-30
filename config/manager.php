<?php
/**
 * Contains the config-manager-class
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The manager for the config-items. Contains config-items of one root-group and can
 * update their values.
 *
 * @package			PHPLib
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_Config_Manager extends PLIB_FullObject
{
	/**
	 * The storage-object
	 *
	 * @var PLIB_Config_Storage
	 */
	private $_storage;
	
	/**
	 * All available groups
	 *
	 * @var array
	 */
	private $_groups;
	
	/**
	 * The loaded items (an array of PLIB_Config_Item's)
	 *
	 * @var array
	 */
	private $_items = null;
	
	/**
	 * Constructor
	 * 
	 * @param PLIB_Config_Storage $storage the storage to use
	 */
	public function __construct($storage)
	{
		parent::__construct();
		
		if(!($storage instanceof PLIB_Config_Storage))
			PLIB_Helper::def_error('instance','storage','PLIB_Config_Storage',$storage);
		
		$this->_storage = $storage;
		
		$this->_groups = array();
		foreach($this->_storage->get_groups() as $group)
			$this->_groups[$group->get_id()] = $group;
	}
	
	/**
	 * @return array an array of PLIB_Config_Group objects
	 */
	public final function get_groups()
	{
		return array_values($this->_groups);
	}
	
	/**
	 * @return int the number of loaded items
	 * @see load_group()
	 * @see load_items_with()
	 */
	public final function get_item_count()
	{
		if($this->_items === null)
			PLIB_Helper::error('Please load the items first, e.g. via load_group()!');
		
		return count($this->_items);
	}
	
	/**
	 * Returns the group with given id
	 *
	 * @param int $id the group-id
	 * @return PLIB_Config_Group the group or null if not found
	 */
	public final function get_group($id)
	{
		if(isset($this->_groups[$id]))
			return $this->_groups[$id];
		
		return null;
	}
	
	/**
	 * Displays the items
	 * 
	 * @param PLIB_Config_View $view the view
	 * @see load_group()
	 * @see load_items_with()
	 */
	public final function display($view)
	{
		if(!($view instanceof PLIB_Config_View))
			PLIB_Helper::def_error('instance','view','PLIB_Config_View',$view);
		if($this->_items === null)
			PLIB_Helper::error('Please load the items first, e.g. via load_group()!');
		
		$last_gid = 0;
		foreach($this->_items as $item)
		{
			$gid = $item->get_data()->get_group_id();
			
			// different group?
			if($this->_groups[$gid]->get_parent_id() > 0 && $last_gid != $gid)
			{
				// end last group?
				if($last_gid != 0)
					$view->end_group($item,$this->_groups[$last_gid]);
				
				$view->begin_group($item,$this->_groups[$gid]);
				$last_gid = $gid;
			}
			
			$view->show_item($item);
		}
		
		// close last group
		if($last_gid != 0 && ($len = count($this->_items)) > 0)
			$view->end_group($this->_items[$len - 1],$this->_groups[$last_gid]);
	}
	
	/**
	 * Reverts the value of the given item to the default value
	 *
	 * @param int $id the item-id
	 * @return boolean true if successfull
	 */
	public final function revert_item($id)
	{
		foreach($this->_items as $item)
		{
			$data = $item->get_data();
			if($data->get_id() == $id)
			{
				$this->_storage->restore_default($id);
				$data->set_value($data->get_default());
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Saves all changes that have been made
	 * 
	 * @return boolean true if something has been changed
	 * @see load_group()
	 * @see load_items_with()
	 */
	public final function save_changes()
	{
		if($this->_items === null)
			PLIB_Helper::error('Please load the items first, e.g. via load_group()!');
		
		$i = 0;
		foreach($this->_items as $item)
		{
			if($item->has_changed())
			{
				$data = $item->get_data();
				$value = $item->get_value();
				$this->_storage->store($data->get_id(),$value);
				$data->set_value($value);
				$i++;
			}
		}
		
		return $i > 0;
	}
	
	/**
	 * Loads all items of the given (root-)group
	 *
	 * @param int $id the id of the group
	 */
	public final function load_group($id)
	{
		$this->_load_items($this->_storage->get_items_of_group($id));
	}
	
	/**
	 * Loads all items with the given keyword
	 *
	 * @param string $keyword the keyword
	 */
	public final function load_items_with($keyword)
	{
		$this->_load_items($this->_storage->get_items_with($keyword));
	}
	
	/**
	 * Loads the given items
	 *
	 * @param array $items an array of PLIB_Config_Data objects
	 */
	protected final function _load_items($items)
	{
		$this->_items = array();
		foreach($items as $data)
		{
			$item = $this->_get_item($data);
			if($item !== null)
				$this->_items[] = $item;
		}
	}
	
	/**
	 * Loads the item for the given data. You may overwrite this method to support additional types
	 * or something similar.
	 *
	 * @param PLIB_Config_Data $data the data of the item
	 * @return PLIB_Config_Item the corresponding item or null if the type is unknown
	 */
	protected function _get_item($data)
	{
		$name = 'PLIB_Config_Item_'.ucfirst($data->get_type());
		$file = PLIB_Path::lib().'config/item/'.$data->get_type().'.php';
		if(is_file($file))
		{
			include_once($file);
			if(class_exists($name))
				return new $name($data);
		}
		
		return null;
	}
	
	protected function _get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>