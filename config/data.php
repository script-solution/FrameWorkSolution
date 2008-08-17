<?php
/**
 * Contains the config-data class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Contains all data of a config-item
 *
 * @package			FrameWorkSolution
 * @subpackage	config
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_Config_Data extends FWS_Object
{
	/**
	 * The id of the entry
	 *
	 * @var int
	 */
	private $_id;
	
	/**
	 * The name of the entry
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * Optional a custom name for the title and description. This gives you for example the
	 * opportunity to provide the same descriptions for different settings
	 *
	 * @var string
	 */
	private $_custom_name;
	
	/**
	 * The id of the group to which this entry belongs
	 * 
	 * @var int
	 */
	private $_group_id;
	
	/**
	 * The sort-key of the item (in its group)
	 *
	 * @var int
	 */
	private $_sort;

	/**
	 * The type of config-item (line,date,enum,..)
	 *
	 * @var string
	 */
	private $_type;
	
	/**
	 * The properties depending on the type. These may be the available values for enums,
	 * the size and maxlength for lines and so on.
	 *
	 * @var array
	 */
	private $_properties;
	
	/**
	 * The language-entry for the suffix or empty if no suffix is required.
	 *
	 * @var string
	 */
	private $_suffix;
	
	/**
	 * The current value
	 *
	 * @var mixed
	 */
	private $_value;
	
	/**
	 * The default value
	 *
	 * @var mixed
	 */
	private $_default;
	
	/**
	 * Constructor
	 *
	 * @param int $id the id
	 * @param string $name the name
	 * @param string $custom_name the custom-name for the title and description (optional)
	 * @param int $group_id the group-id
	 * @param int $sort the sort-key
	 * @param string $type the type
	 * @param string $properties the properties depending on the type
	 * @param string $suffix the language-entry for the suffix
	 * @param mixed $value the current value
	 * @param mixed $default the default value
	 */
	public function __construct($id,$name,$custom_name,$group_id,$sort,$type,$properties,
		$suffix,$value,$default)
	{
		parent::__construct();
		
		$this->_id = $id;
		$this->_name = $name;
		$this->_custom_name = $custom_name;
		$this->_group_id = $group_id;
		$this->_sort = $sort;
		$this->_type = $type;
		$this->_properties = $this->_decode_properties($properties);
		$this->_suffix = $suffix;
		$this->_value = $value;
		$this->_default = $default;
	}
	
	/**
	 * Decodes the properties and returns the result
	 * 
	 * @param string $properties the stored properties
	 * @return array the properties
	 */
	private function _decode_properties($properties)
	{
		$res = array();
		$lines = FWS_Array_Utils::advanced_explode("\n",$properties);
		foreach($lines as $line)
		{
			$parts = explode('=',$line);
			// ignore invalid entries
			if(count($parts) != 2 || $parts[0] == '')
				continue;
			
			$res[$parts[0]] = trim($parts[1]);
		}
		return $res;
	}

	/**
	 * @return int the id
	 */
	public final function get_id()
	{
		return $this->_id;
	}

	/**
	 * @return string the name of the entry
	 */
	public final function get_name()
	{
		return $this->_name;
	}
	
	/**
	 * Returns the name for the title and description. If a custom name is given this one will
	 * be returned. The name otherwise.
	 *
	 * @return string the name for the title and description
	 */
	public final function get_title_name()
	{
		return $this->_custom_name ? $this->_custom_name : $this->_name;
	}

	/**
	 * @return mixed the current value
	 */
	public final function get_value()
	{
		return $this->_value;
	}
	
	/**
	 * Sets the value to the given one
	 *
	 * @param mixed $val the new value
	 */
	public final function set_value($val)
	{
		$this->_value = $val;
	}

	/**
	 * @return mixed the default value
	 */
	public final function get_default()
	{
		return $this->_default;
	}

	/**
	 * @return int the id of the group to which this entry belongs
	 */
	public final function get_group_id()
	{
		return $this->_group_id;
	}

	/**
	 * @return int the sort-key (in its group)
	 */
	public final function get_sort()
	{
		return $this->_sort;
	}

	/**
	 * @return string the type
	 */
	public final function get_type()
	{
		return $this->_type;
	}

	/**
	 * @return array the properties as associative array depending on the type. These may be
	 * 	the available values for enums, the size and maxlength for lines and so on.
	 */
	public final function get_properties()
	{
		return $this->_properties;
	}
	
	/**
	 * @return string the language-entry for the suffix or an empty string for no suffix
	 */
	public final function get_suffix()
	{
		return $this->_suffix;
	}
	
	protected function get_dump_vars()
	{
		return get_object_vars($this);
	}
}
?>