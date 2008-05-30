<?php
/**
 * Contains the enum-class for the additional-fields
 *
 * @version			$Id$
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents an enumeration as additional field. That means multiple predefined values.
 *
 * @package			PHPLib
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class PLIB_AddField_Type_Enum extends PLIB_AddField_Type_Default
{
	protected function _get_formular_field($formular,$value)
	{
		$lines = $this->_data->get_values();
		$lines[-1] = $this->locale->lang('no_choice');
		ksort($lines);
		return $formular->get_combobox('add_'.$this->_data->get_name(),$lines,$value);
	}
	
	protected function _is_valid_value($value)
	{
		$lines = $this->_data->get_values();
		return isset($lines[$value]);
	}
	
	public function is_empty($value)
	{
		return $value == -1;//!$this->_is_valid_value($value);
	}
	
	public function get_value_to_store($value)
	{
		if(!$this->_is_valid_value($value))
			return -1;
		
		return $value;
	}
}
?>