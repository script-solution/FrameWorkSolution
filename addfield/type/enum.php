<?php
/**
 * Contains the enum-class for the additional-fields
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * Represents an enumeration as additional field. That means multiple predefined values.
 *
 * @package			FrameWorkSolution
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_AddField_Type_Enum extends FWS_AddField_Type_Default
{
	protected function get_formular_field_impl($formular,$value)
	{
		$locale = FWS_Props::get()->locale();

		$lines = $this->_data->get_values();
		$lines[-1] = $locale->lang('no_choice');
		ksort($lines);
		return $formular->get_combobox('add_'.$this->_data->get_name(),$lines,$value);
	}
	
	protected function is_valid_value_impl($value)
	{
		$lines = $this->_data->get_values();
		return isset($lines[$value]);
	}

	public function get_default_value()
	{
		$lines = $this->_data->get_values();
		return current($lines);
	}
	
	public function is_empty($value)
	{
		return $value == -1;//!$this->is_valid_value_impl($value);
	}
	
	public function get_value_to_store($value)
	{
		if(!$this->is_valid_value_impl($value))
			return -1;
		
		return $value;
	}
}
?>