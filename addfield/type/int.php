<?php
/**
 * Contains the integer-class for the additional-fields
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents an integer as additional field
 *
 * @package			FrameWorkSolution
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_AddField_Type_Int extends FWS_AddField_Type_Default
{
	protected function get_formular_field_impl($formular,$value)
	{
		if($value == 0)
			$value = '';
		
		$field_size = max(3,min(40,$this->_data->get_length()));
		return $formular->get_textbox(
			'add_'.$this->_data->get_name(),$value,$field_size,$this->_data->get_length()
		);
	}
	
	protected function is_valid_value_impl($value)
	{
		return FWS_Helper::is_integer($value);
	}
	
	public function get_value_to_store($value)
	{
		if(!FWS_Helper::is_integer($value))
			return null;
		
		return $value;
	}
}
?>