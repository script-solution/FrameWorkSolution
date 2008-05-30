<?php
/**
 * Contains the integer-class for the additional-fields
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents an integer as additional field
 *
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_AddField_Type_Int extends PLIB_AddField_Type_Default
{
	protected function _get_formular_field($formular,$value)
	{
		if($value == 0)
			$value = '';
		
		$field_size = max(3,min(40,$this->_data->get_length()));
		return $formular->get_textbox(
			'add_'.$this->_data->get_name(),$value,$field_size,$this->_data->get_length()
		);
	}
	
	protected function _is_valid_value($value)
	{
		return PLIB_Helper::is_integer($value);
	}
	
	public function get_value_to_store($value)
	{
		if(!PLIB_Helper::is_integer($value))
			return null;
		
		return $value;
	}
}
?>