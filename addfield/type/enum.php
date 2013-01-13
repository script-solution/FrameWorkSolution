<?php
/**
 * Contains the enum-class for the additional-fields
 * 
 * @package			FrameWorkSolution
 * @subpackage	addfield.type
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
 * Represents an enumeration as additional field. That means multiple predefined values.
 *
 * @package			FrameWorkSolution
 * @subpackage	addfield.type
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_AddField_Type_Enum extends FWS_AddField_Type_Default
{
	/**
	 * @see FWS_AddField_Type_Default::get_display_value()
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	protected function get_display_value($value)
	{
		$lines = $this->_data->get_values();
		return isset($lines[$value]) ? $lines[$value] : $value;
	}

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
		return $value == '-1';//!$this->is_valid_value_impl($value);
	}
	
	public function get_value_to_store($value)
	{
		if(!$this->is_valid_value_impl($value))
			return '-1';
		
		return $value;
	}
}
?>