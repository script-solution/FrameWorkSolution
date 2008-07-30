<?php
/**
 * Contains the additional-fields-manager-class
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	addfield
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * The manager of the additional fields.
 * Note that this class is a singleton, but has no static get-instance-method since its constructor
 * has a parameter. Please create a subclass of it and provide a get-instance-method for your
 * source.
 *
 * @package			FrameWorkSolution
 * @subpackage	addfield
 * @author			Nils Asmussen <nils@script-solution.de>
 */
abstract class FWS_AddField_Manager extends FWS_Singleton
{
	/**
	 * An associative array of {@link FWS_AddField_Field} objects:
	 * <code>array(<id> => <field>,...)</code>
	 *
	 * @var array
	 */
	private $_fields;
	
	/**
	 * Constructor
	 * 
	 * @param FWS_AddField_Source $source the source for the fields
	 */
	public function __construct($source)
	{
		parent::__construct();
		
		if(!($source instanceof FWS_AddField_Source))
			FWS_Helper::def_error('instance','source','FWS_AddField_Source',$source);
		
		// load all fields
		$fields = $source->get_fields();
		foreach($fields as $data)
		{
			$fieldobj = $source->get_field($data);
			$this->_fields[$data->get_id()] = $fieldobj;
		}
	}
	
	/**
	 * Collects all required fields and returns them
	 *
	 * @return array a numeric array with all required fields
	 */
	public final function get_required_fields()
	{
		$fields = array();
		foreach($this->_fields as $f)
		{
			/* @var $f FWS_AddField_Field */
			if($f->get_data()->is_required())
				$fields[] = $f;
		}
		return $fields;
	}
	
	/**
	 * Returns the field with given name
	 * 
	 * @param string $name the name of the field
	 * @return FWS_AddField_Field the field or null if not found
	 */
	public final function get_field_by_name($name)
	{
		foreach($this->_fields as $f)
		{
			/* @var $f FWS_AddField_Field */
			if($f->get_data()->get_name() == $name)
				return $f;
		}
		
		return null;
	}
	
	/**
	 * Returns the field with given id
	 * 
	 * @param int $id the id of the field
	 * @return FWS_AddField_Field the field or null if not found
	 */
	public final function get_field($id)
	{
		if(isset($this->_fields[$id]))
			return $this->_fields[$id];
		
		return null;
	}
	
	/**
	 * @return array an array of all {@link FWS_AddField_Field} objects
	 */
	public final function get_all_fields()
	{
		return array_values($this->_fields);
	}
	
	/**
	 * Returns an array of all fields at the given locations. Note that you can combine
	 * the locations via the binary-or-operator. That allows you to retrieve fields of multiple
	 * locations.
	 *
	 * @param int $loc the location
	 * @return array an array of {@link FWS_AddField_Field} objects
	 */
	public final function get_fields_at($loc)
	{
		$res = array();
		foreach($this->_fields as $field)
		{
			if(($field->get_data()->get_location() & $loc) != 0)
				$res[] = $field;
		}
		return $res;
	}
	
	protected function get_print_vars()
	{
		return get_object_vars($this);
	}
}
?>