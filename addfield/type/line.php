<?php
/**
 * Contains the line-class for the additional-fields
 *
 * @version			$Id: line.php 540 2008-04-10 06:31:52Z nasmussen $
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents a single line as additional field
 *
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_AddField_Type_Line extends PLIB_AddField_Type_Default
{
	protected function _get_formular_field($formular,$value)
	{
		$field_size = max(3,min(40,$this->_data->get_length()));
		return $formular->get_textbox(
			'add_'.$this->_data->get_name(),$value,$field_size,$this->_data->get_length()
		);
	}
	
	protected function _is_valid_value($value)
	{
		$regex = $this->_data->get_validation();
		if($regex == '' || preg_match($regex,$value) == 1)
			return true;
		
		return false;
	}
}
?>