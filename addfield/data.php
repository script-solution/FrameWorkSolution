<?php
/**
 * Contains the data-class for the additional-fields
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	addfield
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Simply a container for the data of the fields
 * 
 * @package			FrameWorkSolution
 * @subpackage	addfield
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_AddField_Data extends FWS_Object
{
	/**
	 * The id of the field
	 *
	 * @var int
	 */
	private $_id;
	
	/**
	 * The type of the field
	 *
	 * @var string
	 */
	private $_type;
	
	/**
	 * The location-identifier
	 *
	 * @var int
	 */
	private $_location;
	
	/**
	 * The name of the field (as identifier)
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * The title of the field (for displaying)
	 *
	 * @var string
	 */
	private $_title;
	
	/**
	 * The sort-key of the field
	 *
	 * @var int
	 */
	private $_sort;
	
	/**
	 * Wether the field is required. That means the user has to enter a value.
	 *
	 * @var boolean
	 */
	private $_is_required;
	
	/**
	 * The string that should be appended to the formular if the user edits the field
	 *
	 * @var string
	 */
	private $_edit_suffix;
	
	/**
	 * The string that should be appended to the value if the user sees the field
	 *
	 * @var string
	 */
	private $_value_suffix;
	
	/**
	 * Wether the field should be displayed if it is empty
	 *
	 * @var boolean
	 */
	private $_display_empty;
	
	/**
	 * The length of the field
	 *
	 * @var int
	 */
	private $_length;
	
	/**
	 * All possible values of the field
	 *
	 * @var array
	 */
	private $_values;
	
	/**
	 * An regular expression to validate the value
	 *
	 * @var string
	 */
	private $_validation;
	
	/**
	 * If available it contains a custom definition for displaying the field
	 * 
	 * @var string
	 */
	private $_custom_display;
	
	/**
	 * Constructor
	 *
	 * @param int $id the id
	 * @param string $type the type of the field
	 * @param int $location the location-identifier of the field
	 * @param string $name the name (to identify)
	 * @param string $title the title (to display)
	 * @param int $sort the sort-key
	 * @param boolean $is_required wether the field is required
	 * @param string $edit_suffix the suffix for editing
	 * @param string $value_suffix the suffix for the value
	 * @param boolean $display_empty wether the field should be displayed if it is empty
	 * @param int $length the length of the field
	 * @param array $values all possible values of the field
	 * @param string $validation a regular expression to validate the value
	 * @param string $custom_display a custom definition for displaying the field
	 */
	public function __construct($id,$type,$location,$name,$title,$sort = 1,$is_required = false,
		$edit_suffix = '',$value_suffix = '',$display_empty = false,$length = 0,$values = array(),
		$validation = '',$custom_display = '')
	{
		parent::__construct();
		
		if(!FWS_Helper::is_integer($id) || $id <= 0)
			FWS_Helper::def_error('intgt0','id',$id);
		if(!is_string($type) || empty($type))
			FWS_Helper::def_error('notempty','type',$type);
		if(!FWS_Helper::is_integer($location) || $location < 0)
			FWS_Helper::def_error('intge0','location',$location);
		if(!is_string($name) || empty($name))
			FWS_Helper::def_error('notempty','name',$name);
		if(!is_string($title) || empty($title))
			FWS_Helper::def_error('notempty','title',$title);
		if(!FWS_Helper::is_integer($sort))
			FWS_Helper::def_error('integer','sort',$sort);
		if(!FWS_Helper::is_integer($length) || $length < 0)
			FWS_Helper::def_error('intge0','length',$length);
		if(!is_array($values))
			FWS_Helper::def_error('array','values',$values);
		if(!is_string($validation))
			FWS_Helper::def_error('string','validation',$validation);
		
		$this->_id = $id;
		$this->_type = $type;
		$this->_location = $location;
		$this->_name = $name;
		$this->_title = $title;
		$this->_sort = $sort;
		$this->_is_required = (bool)$is_required;
		$this->_edit_suffix = (string)$edit_suffix;
		$this->_value_suffix = (string)$value_suffix;
		$this->_display_empty = (bool)$display_empty;
		$this->_length = $length;
		$this->_values = $values;
		$this->_validation = $validation;
		$this->_custom_display = (string)$custom_display;
	}
	
	/**
	 * @return int the id of the field
	 */
	public final function get_id()
	{
		return $this->_id;
	}
	
	/**
	 * @return string the type of the field as string
	 */
	public final function get_type()
	{
		return $this->_type;
	}
	
	/**
	 * The location-identifier
	 *
	 * @return int the location
	 */
	public final function get_location()
	{
		return $this->_location;
	}
	
	/**
	 * @return string the name of the field. This is used as identifier and will not be used to
	 * 	display the field!
	 */
	public final function get_name()
	{
		return $this->_name;
	}
	
	/**
	 * @return string the title of the field. This will be used to display the field.
	 */
	public function get_title()
	{
		return $this->_title;
	}
	
	/**
	 * @return int the sort-key of the field
	 */
	public final function get_sort()
	{
		return $this->_sort;
	}
	
	/**
	 * @return boolean true if the field is required. That means the user has to fill in the field
	 */
	public final function is_required()
	{
		return $this->_is_required;
	}
	
	/**
	 * @return string the string that should be appended to the value
	 */
	public final function get_value_suffix()
	{
		return $this->_value_suffix;
	}
	
	/**
	 * @return string the string that should be appended if the user edits the field
	 */
	public final function get_edit_suffix()
	{
		return $this->_edit_suffix;
	}
	
	/**
	 * @return boolean wether the field should be displayed if the value is empty
	 */
	public final function display_empty()
	{
		return $this->_display_empty;
	}

	/**
	 * @return int the length of the field
	 */
	public final function get_length()
	{
		return $this->_length;
	}

	/**
	 * @return string the regular expression for the validation of the value
	 */
	public final function get_validation()
	{
		return $this->_validation;
	}

	/**
	 * @return array an array with all possible values
	 */
	public final function get_values()
	{
		return $this->_values;
	}
	
	/**
	 * @return string the custom-display definition of this field
	 */
	public final function get_custom_display()
	{
		return $this->_custom_display;
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>